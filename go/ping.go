package main

import (
	"encoding/json"
	"fmt"
	"net"
	"net/http"
	"os"
	"time"
)

type Result struct {
	LastMs int   `json:"last_ms"`
	AvgMs  int   `json:"avg_ms"`
	Samples []int `json:"samples"`
}

func main() {
	url := "https://dynamodb.ap-southeast-1.amazonaws.com/ping"

	transport := &http.Transport{
		DialContext: (&net.Dialer{
			Timeout:   5 * time.Second,
			KeepAlive: 30 * time.Second,
		}).DialContext,

		ForceAttemptHTTP2: true,
		MaxIdleConns:      100,
		IdleConnTimeout:  90 * time.Second,
	}

	client := &http.Client{
		Timeout:   10 * time.Second,
		Transport: transport,
	}

	// 🔥 warm-up (DNS + TLS)
	doPing(client, url)

	var total int64
	var last int64
	var samples []int

	var pingCount int64 = 20

	for i := 0; i < int(pingCount); i++ {
		ms, err := doPing(client, url)
		if err != nil {
			fmt.Println("error:", err)
			continue
		}
		last = ms
		total += ms
		samples = append(samples, int(ms))
	}

	avg := total / int64(pingCount)
	
	result := Result{
		LastMs: int(last),
		AvgMs:  int(avg),
		Samples: samples,
	}

	json.NewEncoder(os.Stdout).Encode(result)

}

func doPing(client *http.Client, url string) (int64, error) {
	req, err := http.NewRequest("HEAD", url, nil)
	if err != nil {
		return 0, err
	}

	start := time.Now()
	resp, err := client.Do(req)
	elapsed := time.Since(start)

	if err != nil {
		return 0, err
	}

	resp.Body.Close()
	return elapsed.Milliseconds(), nil
}
