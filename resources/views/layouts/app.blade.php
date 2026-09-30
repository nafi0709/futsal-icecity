<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Futsal Ice City')</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #030305; --grid-color: rgba(0, 240, 255, 0.08);
            --building-base: #0c1016; --building-side: #07090c; --building-top: #151d26;
            --neon-ice: #00F0FF; --neon-dim: rgba(0, 240, 255, 0.15);
            --ui-text: #8aaeb5; --white: #ffffff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: var(--bg-color); color: var(--ui-text);
            font-family: 'Share Tech Mono', monospace; overflow-x: hidden; min-height: 100vh;
        }
        .scanlines {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0) 50%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.3));
            background-size: 100% 4px; z-index: 10; pointer-events: none; opacity: 0.2;
        }
        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 40px; z-index: 30; position: relative;
            background: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent);
            border-bottom: 1px solid rgba(0, 240, 255, 0.1);
        }
        .brand { font-family: 'Rajdhani', sans-serif; font-size: 1.8rem; font-weight: 700; color: var(--white); letter-spacing: 2px; text-transform: uppercase; }
        .brand span { color: var(--neon-ice); text-shadow: 0 0 10px var(--neon-ice); }
        
        .scene-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; opacity: 0.3; pointer-events: none;
            display: flex; justify-content: center; align-items: center; perspective: 1000px;
        }
        .city-stage {
            position: relative; transform-style: preserve-3d;
            transform: rotateX(60deg) rotateZ(-45deg); animation: cityRotate 60s linear infinite;
        }
        @keyframes cityRotate { 0% { transform: rotateX(60deg) rotateZ(0deg); } 100% { transform: rotateX(60deg) rotateZ(360deg); } }
        .big-planet {
            position: absolute; width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(0,240,255,0.05) 0%, transparent 70%);
            border: 1px solid rgba(0,240,255,0.3); box-shadow: 0 0 50px rgba(0,240,255,0.1);
            top: 50%; left: 50%; transform: translate(-50%, -50%) translateZ(-100px);
        }
        .grid-base {
            width: 600px; height: 600px;
            background-image: linear-gradient(var(--grid-color) 1px, transparent 1px), linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
            background-size: 50px 50px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        }
        .building { position: absolute; width: 40px; background: var(--building-base); transform-style: preserve-3d; }
        .building::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 40px; background: var(--building-top);
            border: 1px solid rgba(0, 240, 255, 0.1); transform: translateY(-100%) rotateX(90deg); transform-origin: bottom;
        }
        .pillar {
            position: absolute; width: 10px; background: var(--neon-ice);
            box-shadow: 0 0 10px var(--neon-ice), 0 0 20px var(--neon-ice); opacity: 0.9;
            animation: pulseData 2s ease-in-out infinite; transform-style: preserve-3d;
        }
        @keyframes pulseData { 0%, 100% { opacity: 0.7; } 50% { opacity: 1; box-shadow: 0 0 25px var(--neon-ice); } }

        .content-wrapper { position: relative; z-index: 20; max-width: 1200px; margin: 40px auto; padding: 20px; }
        .glass-panel {
            background: rgba(12, 16, 22, 0.9); border: 1px solid var(--neon-ice);
            box-shadow: 0 0 20px var(--neon-dim); padding: 30px; border-radius: 8px;
            backdrop-filter: blur(10px); color: var(--white); margin-bottom: 30px;
        }
        .neon-btn {
            background: transparent; border: 1px solid var(--neon-ice); color: var(--neon-ice);
            padding: 10px 20px; font-family: 'Share Tech Mono', monospace; text-transform: uppercase;
            cursor: pointer; transition: 0.3s; box-shadow: 0 0 10px var(--neon-dim); text-decoration: none; display: inline-block;
        }
        .neon-btn:hover { background: var(--neon-ice); color: var(--bg-color); box-shadow: 0 0 20px var(--neon-ice); }
        .neon-input {
            background: rgba(0, 240, 255, 0.05); border: 1px solid var(--neon-dim); color: var(--white);
            padding: 12px; width: 100%; font-family: 'Share Tech Mono', monospace; margin-bottom: 15px; border-radius: 4px;
        }
        .neon-input:focus { outline: none; border-color: var(--neon-ice); box-shadow: 0 0 10px var(--neon-dim); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid var(--neon-dim); padding: 12px; text-align: left; }
        .data-table th { background: rgba(0, 240, 255, 0.1); color: var(--neon-ice); text-transform: uppercase; }
        .status-pending { color: #ffaa00; }
        .status-paid { color: #00ffaa; }
    </style>
</head>
<body>
    <div class="scanlines"></div>
    <div class="scene-container">
        <div class="city-stage">
            <div class="big-planet"></div>
            <div class="grid-base"></div>
            <div class="building" style="left: 280px; top: 280px; height: 180px;"></div>
            <div class="building" style="left: 230px; top: 230px; height: 100px;"></div>
            <div class="building" style="left: 330px; top: 330px; height: 120px;"></div>
            <div class="pillar" style="left: 295px; top: 280px; height: 250px;"></div>
            <div class="pillar" style="left: 385px; top: 180px; height: 180px;"></div>
        </div>
    </div>

    <header>
        <div class="brand">FUTSAL <span>ICE CITY</span></div>
        <div class="menu-area">
            @auth
                <span style="color: var(--white); margin-right: 15px; font-family: 'Rajdhani', sans-serif; font-weight: 700;">
                    <i class="fa-solid fa-user-astronaut"></i> {{ Auth::user()->name }} [{{ strtoupper(Auth::user()->role) }}]
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="neon-btn" style="padding: 5px 15px; font-size: 0.8rem;">LOGOUT</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="neon-btn" style="text-decoration: none; padding: 5px 15px; font-size: 0.8rem;">LOGIN</a>
            @endauth
        </div>
    </header>

    <div class="content-wrapper">
        @if(session('success'))
            <div class="glass-panel" style="border-color: #00ffaa; color: #00ffaa; text-align: center;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="glass-panel" style="border-color: #ff0055; color: #ff0055; text-align: center;">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>