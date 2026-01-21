<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PING-PULSE - Test Your Gaming Latency</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/tailwind-output.css') }}?v={{ filemtime(public_path('css/tailwind-output.css')) }}"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F0F0F0] font-bold">
    <div id="app">
        <div class="min-h-screen p-4 md:p-8">
            <!-- Header -->
            <header class="mb-8 md:mb-12">
                <div class="max-w-6xl mx-auto text-center">
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-2">
                        PING-PULSE
                    </h1>
                    <p class="text-lg md:text-xl">Test Your Gaming Latency 🎮</p>
                </div>
            </header>

            <!-- Main Content -->
            <main class="max-w-6xl mx-auto">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl md:text-4xl font-black mb-4">SELECT A GAME</h2>
                </div>

                <!-- Mobile: Dropdown -->
                <div class="md:hidden mb-6">
                    <div class="relative">
                        <select v-model="selectedGame"
                            class="w-full px-6 py-4 text-lg font-black border-4 border-black shadow-[4px_4px_0px_0px_#000] bg-white appearance-none cursor-pointer focus:outline-none focus:shadow-[8px_8px_0px_0px_#000] transition-all">
                            <option :value="null" disabled>Choose a game...</option>
                            <option v-for="game in games" :key="game.id" :value="game">
                                @{{ game.icon }} @{{ game.name }} (@{{ game.region }})
                            </option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-2xl">
                            ▼
                        </div>
                    </div>

                    <!-- Selected Game Preview (Mobile) -->
                    <div v-if="selectedGame"
                        class="mt-4 p-6 bg-[#FFDE03] border-4 border-black shadow-[4px_4px_0px_0px_#000]">
                        <div class="text-center">
                            <div class="text-6xl mb-2">@{{ selectedGame.icon }}</div>
                            <h3 class="text-2xl font-black">@{{ selectedGame.name }}</h3>
                            <p class="text-lg">@{{ selectedGame.region }}</p>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Grid -->
                <div class="hidden md:grid grid-cols-3 lg:grid-cols-5 gap-6 mb-12">
                    <div v-for="game in games" :key="game.id" @click="selectGame(game)" :class="[
                            'cursor-pointer bg-white border-4 border-black p-6 text-center transition-all',
                            selectedGame?.id === game.id 
                                ? 'translate-x-1 translate-y-1 shadow-none bg-[#FFDE03]' 
                                : 'shadow-[8px_8px_0px_0px_#000] hover:translate-x-1 hover:translate-y-1 hover:shadow-[4px_4px_0px_0px_#000]'
                        ]">
                        <div class="text-5xl mb-3">@{{ game.icon }}</div>
                        <h3 class="text-lg font-black mb-1">@{{ game.name }}</h3>
                        <p class="text-sm">@{{ game.region }}</p>
                    </div>
                </div>

                <!-- Start Button -->
                <div class="text-center">
                    <button @click="startTest" :disabled="!selectedGame" :class="[
                            'w-full md:w-auto px-12 md:px-16 py-5 md:py-6 text-2xl md:text-3xl font-black border-4 border-black transition-all',
                            selectedGame 
                                ? 'bg-[#00FF00] hover:translate-x-2 hover:translate-y-2 hover:shadow-[4px_4px_0px_0px_#000] shadow-[8px_8px_0px_0px_#000] cursor-pointer active:translate-x-1 active:translate-y-1' 
                                : 'bg-gray-300 cursor-not-allowed opacity-50'
                        ]">
                        START TEST
                    </button>
                </div>
            </main>

            <!-- Modal -->
            <div v-if="showModal"
                class="fixed inset-0 bg-gray-300 bg-opacity-30 flex items-end md:items-center justify-center p-0 md:p-4 z-50"
                @click.self="closeModal">
                <div
                    class="bg-white border-t-8 md:border-8 border-black md:shadow-[16px_16px_0px_0px_#000] w-full md:max-w-2xl md:rounded-none p-6 md:p-8 max-h-[90vh] overflow-y-auto">
                    <!-- Testing Phase -->
                    <div v-if="testing" class="text-center">
                        <h3 class="text-3xl md:text-4xl font-black mb-6">TESTING...</h3>

                        <!-- Countdown -->
                        <div class="text-7xl md:text-8xl font-black mb-6 md:mb-8 text-[#FF0000]">
                            @{{ countdown }}
                        </div>

                        <!-- Status -->
                        <p class="text-lg md:text-xl mb-6 md:mb-8 min-h-[2rem]">@{{ currentStatus }}</p>

                        <!-- Waveform Animation -->
                        <div class="flex items-center justify-center gap-1 md:gap-2 h-24 md:h-32 mb-4">
                            <div v-for="i in 20" :key="i" class="w-2 md:w-3 bg-black transition-all duration-300"
                                :style="{ height: waveHeights[i] + 'px' }"></div>
                        </div>
                    </div>

                    <!-- Result Phase -->
                    <div v-else-if="result" class="text-center">
                        <h3 class="text-2xl md:text-3xl font-black mb-4">RESULT</h3>

                        <!-- Average MS -->
                        <div class="mb-6">
                            <div class="text-7xl md:text-9xl font-black" :style="{ color: result.grade.color }">
                                @{{ result.average }}
                            </div>
                            <div class="text-2xl md:text-3xl font-black">MS</div>
                        </div>

                        <!-- Grade -->
                        <div class="mb-6 md:mb-8 p-4 md:p-6 border-4 border-black bg-[#FFDE03]">
                            <div class="text-5xl md:text-6xl mb-2">@{{ result.grade.emoji }}</div>
                            <div class="text-3xl md:text-4xl font-black">@{{ result.grade.tier }}</div>
                        </div>

                        <!-- Individual Pings -->
                        <div class="mb-6 md:mb-8 flex justify-center gap-2 md:gap-4 flex-wrap">
                            <div v-for="(ping, index) in pings" :key="index"
                                class="px-3 md:px-4 py-2 border-4 border-black bg-white">
                                <div class="text-xs md:text-sm">Ping @{{ index + 1 }}</div>
                                <div class="text-xl md:text-2xl font-black">@{{ ping }}ms</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col md:flex-row gap-3 md:gap-4 justify-center">
                            <button @click="startTest"
                                class="px-6 md:px-8 py-3 md:py-4 bg-[#00FF00] border-4 border-black shadow-[4px_4px_0px_0px_#000] hover:translate-x-1 hover:translate-y-1 hover:shadow-[2px_2px_0px_0px_#000] active:translate-x-0 active:translate-y-0 font-black text-lg md:text-xl">
                                RE-TEST
                            </button>
                            <button @click="closeModal"
                                class="px-6 md:px-8 py-3 md:py-4 bg-white border-4 border-black shadow-[4px_4px_0px_0px_#000] hover:translate-x-1 hover:translate-y-1 hover:shadow-[2px_2px_0px_0px_#000] active:translate-x-0 active:translate-y-0 font-black text-lg md:text-xl">
                                CLOSE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
        window.gamesData = @json($games);
    </script>
    <script src="{{ asset('js/landing.min.js') }}?v={{ filemtime(public_path('js/landing.min.js')) }}"></script>
</body>

</html>