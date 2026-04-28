<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jodoh Murni - Dalam Penyelenggaraan</title>
  <style>
    :root{
      --bg1:#0b1220;
      --bg2:#0b1b2a;
      --text:#eaf2ff;
      --muted: rgba(234,242,255,.78);
    }

    *{ box-sizing: border-box; }
    html, body { height: 100%; }

    body{
      margin:0;
      overflow:hidden;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
      background: radial-gradient(900px 520px at 20% 25%, rgba(34,197,94,.12), transparent 60%),
                  radial-gradient(900px 520px at 80% 30%, rgba(99,102,241,.16), transparent 60%),
                  linear-gradient(135deg, var(--bg1), var(--bg2));
      color: var(--text);
    }

    /* Particles canvas */
    canvas{
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
    }

    /* Center content */
    .center{
      position: relative;
      z-index: 1;
      height: 100vh;
      display: grid;
      place-items: center;
      text-align: center;
      padding: 24px;
    }

    .title{
      font-size: clamp(2.2rem, 5vw, 3.2rem);
      font-weight: 800;
      letter-spacing: -0.8px;
      margin: 0;
    }

    .subtitle{
      margin-top: 10px;
      font-size: clamp(1rem, 2.2vw, 1.25rem);
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 650;
    }

    /* Optional subtle glow behind text */
    .glow{
      display: inline-block;
      padding: 22px 26px;
      border-radius: 20px;
      background: rgba(255,255,255,.03);
      border: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    @media (prefers-reduced-motion: reduce){
      canvas{ display:none; }
    }
  </style>
</head>
<body>
  <canvas id="particles" aria-hidden="true"></canvas>

  <div class="center">
    <div class="glow">
      <h1 class="title">Jodoh Murni</h1>
      <div class="subtitle"><b>Sistem sedang menjalani penyelenggaraan untuk peningkatan prestasi.</b></div>
      <div class="subtitle"><b> Mohon maaf atas segala kesulitan.</b></div>
    </div>
  </div>

  <script>
    // Simple particle background (no libraries)
    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');

    let w = 0, h = 0, dpr = Math.max(1, window.devicePixelRatio || 1);

    function resize(){
      w = window.innerWidth;
      h = window.innerHeight;
      dpr = Math.max(1, window.devicePixelRatio || 1);
      canvas.width = Math.floor(w * dpr);
      canvas.height = Math.floor(h * dpr);
      canvas.style.width = w + 'px';
      canvas.style.height = h + 'px';
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const particles = [];
    const maxParticles = () => {
      // Scale density with screen size, cap for performance
      const base = Math.floor((w * h) / 28000);
      return Math.min(90, Math.max(35, base));
    };

    function rand(min, max){ return Math.random() * (max - min) + min; }

    function init(){
      particles.length = 0;
      const n = maxParticles();
      for(let i=0;i<n;i++){
        particles.push({
          x: rand(0, w),
          y: rand(0, h),
          r: rand(1.2, 3.2),
          vx: rand(-0.35, 0.35),
          vy: rand(-0.25, 0.25),
          a: rand(0.25, 0.75)
        });
      }
    }

    function step(){
      ctx.clearRect(0, 0, w, h);

      // Draw particles
      for(const p of particles){
        p.x += p.vx;
        p.y += p.vy;

        // Wrap around
        if(p.x < -10) p.x = w + 10;
        if(p.x > w + 10) p.x = -10;
        if(p.y < -10) p.y = h + 10;
        if(p.y > h + 10) p.y = -10;

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(234,242,255,${p.a})`;
        ctx.fill();
      }

      // Draw connecting lines for nearby particles
      for(let i=0;i<particles.length;i++){
        for(let j=i+1;j<particles.length;j++){
          const a = particles[i], b = particles[j];
          const dx = a.x - b.x, dy = a.y - b.y;
          const dist = Math.sqrt(dx*dx + dy*dy);
          const maxDist = 130;
          if(dist < maxDist){
            const alpha = (1 - dist / maxDist) * 0.18;
            ctx.strokeStyle = `rgba(234,242,255,${alpha})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.stroke();
          }
        }
      }

      requestAnimationFrame(step);
    }

    function start(){
      resize();
      init();
      if(!prefersReduced) step();
    }

    window.addEventListener('resize', () => {
      resize();
      init();
    }, { passive: true });

    start();
  </script>
</body>
</html>
