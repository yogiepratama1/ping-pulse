const { createApp } = Vue;

createApp({
    data() {
        return {
            games: window.gamesData,
            selectedGame: null,
            showModal: false,
            testing: false,
            countdown: 10,
            currentStatus: '',
            pings: [],
            result: null,
            waveHeights: Array(20).fill(0),
            waveInterval: null
        }
    },
    methods: {
        selectGame(game) {
            this.selectedGame = game;
        },
        async startTest() {
            if (!this.selectedGame) return;

            this.showModal = true;
            this.testing = true;
            this.countdown = 10;
            this.pings = [];
            this.result = null;

            this.startWaveAnimation();

            const statuses = [
                `Fetching ${this.selectedGame.name} ${this.selectedGame.region} Server...`,
                'Sending Packets...',
                'Analyzing Jitter...',
                'Measuring Latency...',
                'Calculating Average...'
            ];

            const interval = setInterval(() => {
                this.countdown--;
                this.currentStatus = statuses[Math.floor(Math.random() * statuses.length)];

                if (this.countdown <= 0) {
                    clearInterval(interval);
                }
            }, 1000);

            try {
                const response = await fetch('/api/ping', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        url: this.selectedGame.url
                    })
                });

                const data = await response.json();

                await new Promise(resolve => setTimeout(resolve, Math.max(0, this.countdown * 1000)));

                this.pings = data.pings;
                this.result = {
                    average: data.average,
                    grade: this.getGrade(data.average)
                };
                this.testing = false;
                this.stopWaveAnimation();
            } catch (error) {
                console.error('Ping test failed:', error);
                this.result = {
                    average: 999,
                    grade: this.getGrade(999)
                };
                this.testing = false;
                this.stopWaveAnimation();
            }
        },
        startWaveAnimation() {
            this.waveInterval = setInterval(() => {
                this.waveHeights = this.waveHeights.map(() => Math.random() * 100 + 20);
            }, 100);
        },
        stopWaveAnimation() {
            if (this.waveInterval) {
                clearInterval(this.waveInterval);
                this.waveInterval = null;
            }
        },
        getGrade(ms) {
            if (ms < 30) return { tier: 'S-TIER', color: '#00FF00', emoji: '🔥' };
            if (ms < 50) return { tier: 'A-TIER', color: '#7FFF00', emoji: '✨' };
            if (ms < 80) return { tier: 'B-TIER', color: '#FFDE03', emoji: '👍' };
            if (ms < 120) return { tier: 'MEH', color: '#FFA500', emoji: '😐' };
            return { tier: 'LAGGY', color: '#FF0000', emoji: '💀' };
        },
        closeModal() {
            this.showModal = false;
            this.testing = false;
            this.result = null;
            this.stopWaveAnimation();
        }
    }
}).mount('#app');
