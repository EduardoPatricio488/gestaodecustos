<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>🔒 Lock In Mode — <?php echo e(config('app.name')); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    
    <script>
        (function() {
            var saved = localStorage.getItem('lockin_theme');
            if (!saved) {
                var global = localStorage.getItem('theme');
                saved = (global === 'dark' || (!global && window.matchMedia('(prefers-color-scheme: dark)').matches)) ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-lockin-theme', saved);
            document.documentElement.classList.toggle('dark', saved === 'dark');
        })();
    </script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ── Variáveis de tema ───────────────── */
        :root {
            --li-bg:        #050505;
            --li-surface:   rgba(255,255,255,0.03);
            --li-border:    rgba(255,255,255,0.06);
            --li-sep:       rgba(255,255,255,0.05);
            --li-text:      #ffffff;
            --li-muted:     #71717a;
            --li-scrollbar: rgba(255,255,255,0.10);
        }
        html[data-lockin-theme="light"] {
            --li-bg:        #f1f5f9;
            --li-surface:   rgba(255,255,255,0.85);
            --li-border:    rgba(0,0,0,0.08);
            --li-sep:       rgba(0,0,0,0.06);
            --li-text:      #0f172a;
            --li-muted:     #64748b;
            --li-scrollbar: rgba(0,0,0,0.15);
        }

        body {
            background: var(--li-bg);
            color: var(--li-text);
            overflow: hidden;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .tabular { font-variant-numeric: tabular-nums; }

        /* Glow borders */
        .glow-green  { box-shadow: 0 0 20px rgba(16,185,129,0.15), inset 0 0 20px rgba(16,185,129,0.03); border-color: rgba(16,185,129,0.25) !important; }
        .glow-orange { box-shadow: 0 0 20px rgba(249,115,22,0.15), inset 0 0 20px rgba(249,115,22,0.03); border-color: rgba(249,115,22,0.25) !important; }
        .glow-red    { box-shadow: 0 0 20px rgba(239,68,68,0.15),  inset 0 0 20px rgba(239,68,68,0.03);  border-color: rgba(239,68,68,0.25)  !important; }
        .glow-blue   { box-shadow: 0 0 20px rgba(59,130,246,0.15), inset 0 0 20px rgba(59,130,246,0.03); border-color: rgba(59,130,246,0.25) !important; }

        /* Glass panels */
        .glass {
            background: var(--li-surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--li-border);
        }

        /* Privacy blur */
        .privacy-active .privacy-val {
            filter: blur(10px);
            transition: filter 0.3s ease;
        }
        .privacy-active .privacy-val:hover {
            filter: blur(0px);
        }

        /* Scanline overlay */
        .scanlines::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.03) 2px,
                rgba(0,0,0,0.03) 4px
            );
            pointer-events: none;
            z-index: 1;
        }

        /* Hold-to-unlock progress */
        @keyframes holdProgress {
            from { width: 0%; }
            to   { width: 100%; }
        }
        .hold-active .hold-bar {
            animation: holdProgress 3s linear forwards;
        }

        /* Animated gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #10b981 0%, #34d399 50%, #6ee7b7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Number counter animation */
        .num-highlight {
            text-shadow: 0 0 30px rgba(16,185,129,0.5);
        }

        /* Runway danger */
        .runway-critical { color: #ef4444 !important; text-shadow: 0 0 30px rgba(239,68,68,0.6); }
        .runway-warning  { color: #f97316 !important; text-shadow: 0 0 30px rgba(249,115,22,0.6); }
        .runway-safe     { color: #10b981 !important; text-shadow: 0 0 30px rgba(16,185,129,0.6); }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--li-scrollbar); border-radius: 10px; }

        /* Pulse ring */
        @keyframes pulseRing {
            0%   { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1px solid rgba(16,185,129,0.5);
            animation: pulseRing 2s ease-out infinite;
        }

        /* Entry animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-1 { animation: fadeInUp 0.4s ease forwards; }
        .anim-2 { animation: fadeInUp 0.5s ease 0.1s both; }
        .anim-3 { animation: fadeInUp 0.5s ease 0.2s both; }
        .anim-4 { animation: fadeInUp 0.5s ease 0.3s both; }
        .anim-5 { animation: fadeInUp 0.5s ease 0.4s both; }
        .anim-6 { animation: fadeInUp 0.5s ease 0.5s both; }
    </style>
</head>
<body class="min-h-screen w-full scanlines">

    <?php echo e($slot); ?>


    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/components/layouts/lockin.blade.php ENDPATH**/ ?>