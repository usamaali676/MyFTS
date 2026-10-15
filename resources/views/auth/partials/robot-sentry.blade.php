{{--
    Robot Sentry mascot — shields its eyes while a password/OTP is being typed,
    peeks when the value is revealed / complete.

    Usage:
      @include('auth.partials.robot-sentry', [
          'botId'  => 'loginBot',       // unique id prefix for this instance
          'mode'   => 'password',       // 'password' or 'otp'
          'target' => '#password',      // input selector this bot reacts to
          'toggle' => '#passwordVisibilityToggle', // password mode only: the show/hide element
          'length' => 6,                // otp mode only: expected digit count
      ])

    Colors are pulled from the existing Bootstrap theme variables
    (--bs-primary, --bs-success, --bs-danger, --bs-gray-rgb) so it always
    matches the page's current color scheme / light-dark theme.
--}}
@php
    $botId = $botId ?? 'rsBot'.uniqid();
    $mode = $mode ?? 'password';
    $length = $length ?? 6;
@endphp

<div class="robot-sentry text-center mb-3"
     id="{{ $botId }}"
     data-mode="{{ $mode }}"
     data-target="{{ $target }}"
     @if($mode === 'password') data-toggle="{{ $toggle }}" @endif
     @if($mode === 'otp') data-length="{{ $length }}" @endif>
  <div class="rs-stage">
    <div class="rs-antenna"></div>
    <div class="rs-bubble"></div>
    <div class="rs-bot">
      <div class="rs-hands">
        <div class="rs-hand rs-hand-left"></div>
        <div class="rs-hand rs-hand-right"></div>
      </div>
      <div class="rs-head">
        <div class="rs-face">
          <div class="rs-eye rs-eye-l rs-blink"></div>
          <div class="rs-eye rs-eye-r rs-blink"></div>
        </div>
      </div>
    </div>
    @if($mode === 'otp')
      <div class="rs-confetti"></div>
    @endif
  </div>
  @if($mode === 'otp')
    <div class="rs-meter"></div>
  @endif
  <div class="rs-helper">&nbsp;</div>
</div>

@once
<style>
  .robot-sentry{ --rs-glow: var(--bs-primary); --rs-glow-rgb: var(--bs-primary-rgb); }
  .robot-sentry .rs-stage{
    height:110px;
    display:flex;
    align-items:flex-end;
    justify-content:center;
    position:relative;
  }
  .robot-sentry .rs-antenna{
    position:absolute;
    width:3px; height:14px;
    background:rgba(var(--bs-gray-rgb), .5);
    border-radius:2px;
    left:50%; top:2px;
    transform-origin:bottom center;
    animation:rsAntennaBob 2.6s ease-in-out infinite;
  }
  .robot-sentry .rs-antenna::after{
    content:"";
    position:absolute; top:-6px; left:50%; transform:translateX(-50%);
    width:7px; height:7px; border-radius:50%;
    background:var(--rs-glow);
    box-shadow:0 0 8px 1px rgba(var(--rs-glow-rgb), .6);
  }
  @keyframes rsAntennaBob{ 0%,100%{ transform:translateX(-50%) rotate(0deg);} 50%{ transform:translateX(-50%) rotate(6deg);} }

  .robot-sentry .rs-bot{ width:82px; height:76px; position:relative; margin-top:16px; }
  .robot-sentry .rs-head{
    width:100%; height:100%;
    background:rgba(var(--bs-gray-rgb), .08);
    border:1px solid rgba(var(--bs-gray-rgb), .12);
    border-radius:24px;
    position:relative;
    transition:transform .25s ease;
  }
  .robot-sentry .rs-face{
    position:absolute; inset:14px 10px 22px;
    background:rgba(var(--bs-gray-rgb), .85);
    border-radius:12px;
    display:flex; align-items:center; justify-content:center; gap:11px;
    overflow:hidden;
  }
  .robot-sentry .rs-eye{
    width:12px; height:12px; border-radius:50%;
    background:var(--rs-glow);
    box-shadow:0 0 8px 2px rgba(var(--rs-glow-rgb), .55);
    transition:background .18s ease, box-shadow .18s ease, height .18s ease, transform .18s ease;
  }
  .robot-sentry .rs-eye.rs-dim{ background:rgba(255,255,255,.25); box-shadow:none; height:2px; border-radius:2px; }
  .robot-sentry .rs-eye.rs-blink{ animation:rsBlink 3.6s ease-in-out infinite; }
  @keyframes rsBlink{ 0%,92%,100%{ transform:scaleY(1);} 96%{ transform:scaleY(.15);} }

  .robot-sentry .rs-hands{ position:absolute; inset:0; pointer-events:none; }
  .robot-sentry .rs-hand{
    position:absolute; width:30px; height:24px; top:54px;
    background:rgba(var(--bs-gray-rgb), .1);
    border:1px solid rgba(var(--bs-gray-rgb), .16);
    border-radius:14px 14px 16px 16px;
    transition:top .3s cubic-bezier(.34,1.4,.64,1), transform .3s cubic-bezier(.34,1.4,.64,1);
  }
  .robot-sentry .rs-hand-left{ left:-5px; transform:rotate(-8deg); }
  .robot-sentry .rs-hand-right{ right:-5px; transform:rotate(8deg); }
  .robot-sentry .rs-bot.rs-covering .rs-hand-left{ top:20px; transform:rotate(6deg); }
  .robot-sentry .rs-bot.rs-covering .rs-hand-right{ top:20px; transform:rotate(-6deg); }
  .robot-sentry .rs-bot.rs-peeking .rs-hand-left{ top:20px; transform:rotate(6deg); }
  .robot-sentry .rs-bot.rs-peeking .rs-hand-right{ top:50px; transform:rotate(18deg) translateX(3px); }
  .robot-sentry .rs-bot.rs-covering .rs-head{ transform:translateY(2px) rotate(-1deg); }

  .robot-sentry .rs-bot.rs-glitch .rs-head{ animation:rsGlitch .28s steps(2); }
  @keyframes rsGlitch{
    0%{ filter:none; transform:translate(0,0); }
    30%{ filter:drop-shadow(2px 0 var(--bs-primary)) drop-shadow(-2px 0 var(--bs-danger)); transform:translate(1px,-1px); }
    60%{ filter:drop-shadow(-2px 0 var(--bs-primary)) drop-shadow(2px 0 var(--bs-danger)); transform:translate(-1px,1px); }
    100%{ filter:none; transform:translate(0,0); }
  }

  .robot-sentry .rs-bubble{
    position:absolute; top:-4px; left:50%;
    transform:translate(-50%,-8px);
    background:rgba(var(--bs-gray-rgb), .9);
    color:#fff;
    font-size:11px;
    padding:5px 9px;
    border-radius:8px;
    white-space:nowrap;
    opacity:0;
    transition:opacity .2s ease, transform .2s ease;
    pointer-events:none;
  }
  .robot-sentry .rs-bubble::after{
    content:"";
    position:absolute; bottom:-5px; left:50%; transform:translateX(-50%);
    border:5px solid transparent; border-top-color:rgba(var(--bs-gray-rgb), .9);
  }
  .robot-sentry .rs-bubble.rs-show{ opacity:1; transform:translate(-50%,-14px); }

  .robot-sentry .rs-helper{
    font-size:12px; color:var(--bs-body-color); margin-top:4px; min-height:16px;
  }

  .robot-sentry .rs-meter{ display:flex; gap:5px; justify-content:center; margin-bottom:2px; }
  .robot-sentry .rs-meter i{
    width:13px; height:6px; border-radius:3px;
    background:rgba(var(--bs-gray-rgb), .15);
    transition:background .2s ease, box-shadow .2s ease;
  }
  .robot-sentry .rs-meter i.rs-lit{
    background:var(--rs-glow);
    box-shadow:0 0 8px rgba(var(--rs-glow-rgb), .5);
  }

  .robot-sentry .rs-confetti{ position:absolute; inset:0; pointer-events:none; overflow:hidden; }
  .robot-sentry .rs-confetti span{
    position:absolute; top:-10px; width:5px; height:9px; border-radius:1px; opacity:0;
  }
  @keyframes rsConfettiFall{
    0%{ opacity:1; transform:translateY(0) rotate(0deg); }
    100%{ opacity:0; transform:translateY(110px) rotate(200deg); }
  }

  @media (prefers-reduced-motion: reduce){
    .robot-sentry *{ animation:none !important; transition:none !important; }
  }
</style>
@endonce

@once
<script>
document.addEventListener('DOMContentLoaded', function(){
  var confettiColors = ['var(--bs-primary)','var(--bs-success)','var(--bs-danger)','rgba(var(--bs-gray-rgb),.6)'];

  function showBubble(root, text, ms){
    var el = root.querySelector('.rs-bubble');
    if(!el) return;
    el.textContent = text;
    el.classList.add('rs-show');
    clearTimeout(el._rsTimer);
    el._rsTimer = setTimeout(function(){ el.classList.remove('rs-show'); }, ms || 1500);
  }

  function burstConfetti(root){
    var wrap = root.querySelector('.rs-confetti');
    if(!wrap) return;
    for(var n = 0; n < 14; n++){
      var s = document.createElement('span');
      s.style.left = (20 + Math.random()*60) + '%';
      s.style.background = confettiColors[n % confettiColors.length];
      s.style.animation = 'rsConfettiFall ' + (0.7 + Math.random()*0.5) + 's ease-out forwards';
      s.style.animationDelay = (Math.random()*0.15) + 's';
      s.style.transform = 'rotate(' + (Math.random()*360) + 'deg)';
      wrap.appendChild(s);
      (function(el){ setTimeout(function(){ el.remove(); }, 1400); })(s);
    }
  }

  document.querySelectorAll('.robot-sentry').forEach(function(root){
    var mode = root.dataset.mode;
    var input = document.querySelector(root.dataset.target);
    if(!input) return;

    var bot = root.querySelector('.rs-bot');
    var eyeL = root.querySelector('.rs-eye-l');
    var eyeR = root.querySelector('.rs-eye-r');
    var helper = root.querySelector('.rs-helper');

    function idleEyes(){
      eyeL.classList.remove('rs-dim'); eyeR.classList.remove('rs-dim');
      eyeL.classList.add('rs-blink'); eyeR.classList.add('rs-blink');
    }
    function dimEyes(){
      eyeL.classList.add('rs-dim'); eyeR.classList.add('rs-dim');
      eyeL.classList.remove('rs-blink'); eyeR.classList.remove('rs-blink');
    }

    if(mode === 'password'){
      var toggle = root.dataset.toggle ? document.querySelector(root.dataset.toggle) : null;
      var icon = toggle ? toggle.querySelector('i') : null;
      var revealed = false;
      var lines = ['beep. boop.', 'not peeking, promise.', 'encrypting eyeballs…'];

      function setCovering(on){
        bot.classList.toggle('rs-covering', on && !revealed);
        if(on && !revealed){
          dimEyes();
          helper.textContent = '🙈 shielding its optics';
          if(Math.random() < 0.5) showBubble(root, lines[Math.floor(Math.random()*lines.length)]);
        }
      }

      input.addEventListener('input', function(){ setCovering(input.value.length > 0); });
      input.addEventListener('blur', function(){
        if(input.value.length === 0){
          bot.classList.remove('rs-covering');
          idleEyes();
          helper.innerHTML = '&nbsp;';
        }
      });

      if(toggle){
        toggle.style.cursor = 'pointer';
        toggle.addEventListener('click', function(){
          revealed = !revealed;
          input.type = revealed ? 'text' : 'password';
          if(icon){
            icon.classList.toggle('mdi-eye-off-outline', !revealed);
            icon.classList.toggle('mdi-eye-outline', revealed);
          }
          if(revealed){
            bot.classList.remove('rs-covering');
            bot.classList.add('rs-peeking', 'rs-glitch');
            eyeL.classList.remove('rs-dim'); eyeL.classList.add('rs-blink');
            eyeR.classList.add('rs-dim'); eyeR.classList.remove('rs-blink');
            helper.textContent = '👀 caught a peek';
            showBubble(root, 'oh! there it is 👀', 1600);
            setTimeout(function(){ bot.classList.remove('rs-glitch'); }, 300);
          } else {
            bot.classList.remove('rs-peeking');
            eyeR.classList.remove('rs-dim');
            if(input.value.length > 0){ setCovering(true); }
            else { idleEyes(); helper.innerHTML = '&nbsp;'; }
          }
        });
      }
    }

    if(mode === 'otp'){
      var length = parseInt(root.dataset.length || '6', 10);
      var meter = root.querySelector('.rs-meter');
      if(meter){
        for(var i = 0; i < length; i++){
          meter.appendChild(document.createElement('i'));
        }
      }
      var meterLights = meter ? meter.querySelectorAll('i') : [];
      var celebrated = false;

      input.addEventListener('input', function(){
        var filled = input.value.replace(/\D/g, '').length;
        meterLights.forEach(function(l, idx){ l.classList.toggle('rs-lit', idx < filled); });

        if(filled === 0){
          bot.classList.remove('rs-covering', 'rs-peeking');
          idleEyes();
          helper.innerHTML = '&nbsp;';
          celebrated = false;
          return;
        }
        if(filled < length){
          bot.classList.remove('rs-peeking');
          bot.classList.add('rs-covering');
          dimEyes();
          helper.textContent = 'decoding digit ' + filled + ' of ' + length + '…';
          if(filled === 1) showBubble(root, 'counting… 🔢');
          celebrated = false;
        } else if(!celebrated){
          celebrated = true;
          bot.classList.remove('rs-covering');
          bot.classList.add('rs-peeking');
          idleEyes();
          helper.textContent = '✅ all digits in — verifying';
          showBubble(root, 'got it all! 🎉', 1800);
          burstConfetti(root);
          setTimeout(function(){ bot.classList.remove('rs-peeking'); }, 900);
        }
      });
    }
  });
});
</script>
@endonce
