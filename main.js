// ── 헤더 스크롤 효과 ──
const header = document.getElementById('header');
window.addEventListener('scroll', () => {
  header.classList.toggle('scrolled', window.scrollY > 20);
});

// ── 폰 슬라이더 ──
const screenSlides = document.querySelectorAll('.screen-slide');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const dotsContainer = document.getElementById('slideDots');
let current = 0;

// 도트 생성
screenSlides.forEach((_, i) => {
  const dot = document.createElement('span');
  dot.className = 'slide-dot' + (i === 0 ? ' active' : '');
  dot.addEventListener('click', () => goTo(i));
  dotsContainer.appendChild(dot);
});

function goTo(n) {
  screenSlides[current].classList.remove('active');
  dotsContainer.children[current].classList.remove('active');
  current = (n + screenSlides.length) % screenSlides.length;
  screenSlides[current].classList.add('active');
  dotsContainer.children[current].classList.add('active');
}

prevBtn.addEventListener('click', () => goTo(current - 1));
nextBtn.addEventListener('click', () => goTo(current + 1));

// 4초 자동 슬라이드
let autoSlide = setInterval(() => goTo(current + 1), 4000);
[prevBtn, nextBtn].forEach(btn => {
  btn.addEventListener('click', () => {
    clearInterval(autoSlide);
    autoSlide = setInterval(() => goTo(current + 1), 4000);
  });
});

// ── 스크롤 등장 애니메이션 (Intersection Observer) ──
const aosEls = document.querySelectorAll('[data-aos]');
const observer = new IntersectionObserver(entries => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      // 카드별 순차 딜레이
      setTimeout(() => entry.target.classList.add('visible'), i * 100);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

aosEls.forEach(el => observer.observe(el));
