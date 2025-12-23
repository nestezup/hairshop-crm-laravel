<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hairshop CRM - 프리미엄 살롱 관리 시스템</title>
    <!-- Custom Font: Paperozi (Paperlogy) -->
    <style>
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-1Thin.woff2') format('woff2');
            font-weight: 100;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-2ExtraLight.woff2') format('woff2');
            font-weight: 200;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-3Light.woff2') format('woff2');
            font-weight: 300;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-4Regular.woff2') format('woff2');
            font-weight: 400;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-5Medium.woff2') format('woff2');
            font-weight: 500;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-6SemiBold.woff2') format('woff2');
            font-weight: 600;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-7Bold.woff2') format('woff2');
            font-weight: 700;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-8ExtraBold.woff2') format('woff2');
            font-weight: 800;
            font-display: swap;
        }
        @font-face {
            font-family: 'Paperozi';
            src: url('https://cdn.jsdelivr.net/gh/projectnoonnu/2408-3@1.0/Paperlogy-9Black.woff2') format('woff2');
            font-weight: 900;
            font-display: swap;
        }

        body { 
            font-family: 'Paperozi', sans-serif; 
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(15, 15, 15, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-black text-gray-900 dark:text-gray-100 overflow-x-hidden">
    <div class="relative min-h-screen flex flex-col">
        <!-- Background Gradients -->
        <div class="absolute inset-0 overflow-hidden -z-10">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-orange-400/20 blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[10%] w-[35%] h-[35%] rounded-full bg-purple-400/20 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[30%] h-[30%] rounded-full bg-blue-400/10 blur-[120px]"></div>
        </div>

        <!-- Navigation -->
        <nav class="w-full px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-black dark:bg-white rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L6.243 17.757M12 12l-2.879-2.879M12 12l2.879 2.879m0 0l2.879 2.879M12 12l2.879-2.879" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight">HAIRSHOP <span class="text-orange-500">CRM</span></span>
            </div>
            <a href="/admin" class="px-6 py-2.5 bg-black dark:bg-white text-white dark:text-black font-semibold rounded-full hover:scale-105 transition-transform duration-200 shadow-lg">
                관리자 포털
            </a>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col items-center justify-center px-6 py-12">
            <div class="max-w-4xl w-full text-center space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-orange-500">최고급 살롱 관리 솔루션</h2>
                    <h1 class="text-5xl md:text-7xl font-bold tracking-tighter">
                        프리미엄 헤어샵 관리의 시작, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-rose-500">더 쉽고 완벽하게.</span>
                    </h1>
                    <p class="max-w-2xl mx-auto text-lg text-gray-500 dark:text-gray-400">
                        현대적인 헤어샵을 위한 올인원 시스템. 고객 관리부터 일정 예약까지, 데이터 기반의 스마트한 경영을 경험하세요.
                    </p>
                </div>

                <!-- CTA & Credentials -->
                <div class="flex flex-col items-center gap-6 pt-4">
                    <a href="/admin/login" class="group relative px-8 py-4 bg-orange-500 text-white rounded-2xl font-bold text-lg overflow-hidden transition-all duration-300 hover:shadow-[0_0_40px_rgba(249,115,22,0.4)]">
                        <span class="relative z-10 flex items-center gap-2">
                             지금 시작하기
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-rose-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>

                    <!-- Credentials Card -->
                    <div class="glass p-6 rounded-3xl w-full max-w-md shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-3 opacity-20 group-hover:opacity-100 transition-opacity">
                            <svg class="w-12 h-12 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                        </div>
                        <h3 class="text-left font-bold text-gray-400 uppercase text-xs tracking-widest mb-4">데모 접속 정보</h3>
                        <div class="space-y-4">
                            <div class="flex flex-col text-left">
                                <span class="text-xs text-gray-400">이메일</span>
                                <span class="text-md font-mono font-semibold text-gray-700 dark:text-gray-200">admin@hairshop.com</span>
                            </div>
                            <div class="flex flex-col text-left">
                                <span class="text-xs text-gray-400">비밀번호</span>
                                <span class="text-md font-mono font-semibold text-gray-700 dark:text-gray-200">password</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200/30 dark:border-gray-700/30 text-xs text-center text-gray-400">
                            통계 분석 및 관리 도구가 포함된 모든 기능을 자유롭게 테스트해보세요.
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Banner Image Placeholder -->
        <div class="w-full max-w-7xl mx-auto px-6 pb-12">
            <div class="relative aspect-video md:aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl border border-white/20">
                <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?q=80&w=2000&auto=format&fit=crop" 
                     alt="Premium Salon" 
                     class="w-full h-full object-cover grayscale-[0.2] group-hover:scale-105 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-8">
                    <p class="text-white text-xl font-bold italic tracking-tight opacity-80 uppercase">Experience Excellence</p>
                </div>
            </div>
        </div>

        <footer class="w-full py-8 text-center text-sm text-gray-400">
            &copy; 2025 Hairshop CRM. Elite Salons을 위한 프리미엄 관리 시스템.
        </footer>
    </div>
</body>
</html>
