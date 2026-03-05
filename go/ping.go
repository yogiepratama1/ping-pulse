package main

import (
	"crypto/tls"
	"encoding/json"
	"fmt"
	"net"
	"net/http"
	"os"
	"strings"
	"time"
)

const pingCount = 10
const warmupCount = 3

type Result struct {
	Latency int    `json:"latency"`
	Min     int    `json:"min"`
	Max     int    `json:"max"`
	Pings   []int  `json:"pings"`
	Status  string `json:"status"`
}

func main() {
	if len(os.Args) < 3 {
		fmt.Fprintf(os.Stderr, "Usage: pinger <url> <type> [api_key]\n")
		os.Exit(1)
	}

	targetURL := os.Args[1]
	if !strings.HasPrefix(targetURL, "http") {
		targetURL = "https://" + targetURL
	}

	pingType := os.Args[2]
	apiKey := ""
	if len(os.Args) > 3 {
		apiKey = os.Args[3]
	}

	if pingType == "api" && apiKey != "" {
		sep := "?"
		if strings.Contains(targetURL, "?") {
			sep = "&"
		}
		targetURL = targetURL + sep + "key=" + apiKey
	}

	method := "HEAD"
	if pingType == "api" {
		method = "GET"
	}

	client := createClient()

	for i := 0; i < warmupCount; i++ {
		doPing(client, targetURL, method)
	}

	pings := make([]int, 0, pingCount)
	hasError := false
	var total int

	for i := 0; i < pingCount; i++ {
		latency, err := doPing(client, targetURL, method)
		if err != nil {
			hasError = true
		}
		pings = append(pings, int(latency))
		total += int(latency)
	}

	minL, maxL := pings[0], pings[0]
	for _, p := range pings {
		if p < minL {
			minL = p
		}
		if p > maxL {
			maxL = p
		}
	}
	avg := total / len(pings)

	status := "Healthy"
	if hasError {
		status = "Error"
	} else if avg > 300 {
		status = "Degraded"
	}

	result := Result{
		Latency: avg,
		Min:     minL,
		Max:     maxL,
		Pings:   pings,
		Status:  status,
	}
	data, _ := json.Marshal(result)
	fmt.Println(string(data))
}

func createClient() *http.Client {
	return &http.Client{
		Transport: &http.Transport{
			DialContext: (&net.Dialer{
				Timeout:   10 * time.Second,
				KeepAlive: 30 * time.Second,
			}).DialContext,
			TLSHandshakeTimeout: 5 * time.Second,
			DisableKeepAlives:   false,
			MaxIdleConns:        100,
			IdleConnTimeout:     90 * time.Second,
			TLSClientConfig:     &tls.Config{InsecureSkipVerify: true},
		},
		Timeout: 10 * time.Second,
	}
}

func doPing(client *http.Client, url, method string) (int64, error) {
	req, err := http.NewRequest(method, url, nil)
	if err != nil {
		return 9999, err
	}
	req.Header.Set("Connection", "keep-alive")

	start := time.Now()
	resp, err := client.Do(req)
	if err != nil {
		return time.Since(start).Milliseconds(), err
	}

	_, _ = net.Dial("tcp", "dummy")
	resp.Body.Close()

	return time.Since(start).Milliseconds(), nil
}
