const themeButton = document.querySelector('#themeToggle');
const syncThemeButton = () => {
  const dark = document.body.classList.contains('dark-ui');
  themeButton?.setAttribute('aria-label', dark ? 'Activer le mode clair' : 'Activer le mode sombre');
  themeButton?.setAttribute('title', dark ? 'Mode clair' : 'Mode sombre');
  themeButton?.setAttribute('aria-pressed', dark ? 'true' : 'false');
  const themeLabel = themeButton?.querySelector('.admin-theme-label');
  if (themeLabel) themeLabel.textContent = dark ? 'Mode clair' : 'Mode sombre';
};
if (localStorage.getItem('media-spotlight-theme') === 'dark') document.body.classList.add('dark-ui');
syncThemeButton();
themeButton?.addEventListener('click', () => {
  document.body.classList.toggle('dark-ui');
  localStorage.setItem('media-spotlight-theme', document.body.classList.contains('dark-ui') ? 'dark' : 'light');
  syncThemeButton();
});

const menuButton = document.querySelector('#mobileMenu');
const mainNav = document.querySelector('#mainNav');
menuButton?.addEventListener('click', () => {
  const open = document.body.classList.toggle('menu-visible');
  menuButton.setAttribute('aria-expanded', String(open));
  menuButton.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
});
mainNav?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => document.body.classList.remove('menu-visible')));
document.addEventListener('keydown', event => { if (event.key === 'Escape') document.body.classList.remove('menu-visible'); });

document.querySelectorAll('[data-confirm]').forEach(button => button.addEventListener('click', event => {
  if (!confirm(button.dataset.confirm)) event.preventDefault();
}));

const toast = document.querySelector('.flash-toast');
const closeToast = () => { toast?.classList.add('leaving'); setTimeout(() => toast?.remove(), 240); };
toast?.querySelector('.toast-close')?.addEventListener('click', closeToast);
if (toast) setTimeout(closeToast, 5200);

const titleInput = document.querySelector('#mediaTitle');
const titleCount = document.querySelector('#titleCount');
titleInput?.addEventListener('input', () => { titleCount.textContent = titleInput.value.length; });

const fileInput = document.querySelector('#mediaFile');
const dropZone = document.querySelector('#dropZone');
const preview = document.querySelector('#filePreview');
const fileName = document.querySelector('#fileName');
const fileMeta = document.querySelector('#fileMeta');
const removeFile = document.querySelector('#removeFile');
const formatSize = bytes => bytes < 1048576 ? `${(bytes / 1024).toFixed(0)} Ko` : `${(bytes / 1048576).toFixed(1)} Mo`;
const showFile = file => {
  if (!file || !preview) return;
  fileName.textContent = file.name;
  fileMeta.textContent = `${formatSize(file.size)} · ${file.type || 'Type inconnu'}`;
  preview.hidden = false; dropZone.classList.add('has-file');
};
fileInput?.addEventListener('change', () => showFile(fileInput.files[0]));
dropZone?.addEventListener('click', event => { if (event.target !== fileInput) fileInput.click(); });
dropZone?.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') fileInput.click(); });
['dragenter','dragover'].forEach(name => dropZone?.addEventListener(name, event => { event.preventDefault(); dropZone.classList.add('dragging'); }));
['dragleave','drop'].forEach(name => dropZone?.addEventListener(name, event => { event.preventDefault(); dropZone.classList.remove('dragging'); }));
dropZone?.addEventListener('drop', event => { if (event.dataTransfer.files.length) { fileInput.files = event.dataTransfer.files; showFile(fileInput.files[0]); } });
removeFile?.addEventListener('click', () => { fileInput.value=''; preview.hidden=true; dropZone.classList.remove('has-file'); });

const uploadForm = document.querySelector('#uploadForm');
const publishButton = document.querySelector('#publishButton');
uploadForm?.addEventListener('submit', () => {
  publishButton.disabled = true;
  publishButton.classList.add('loading');
  publishButton.querySelector('span').textContent = 'Publication en cours…';
});

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealTargets = document.querySelectorAll('.home-section, .home-stats, .home-cta, .audience-section');
if (!reducedMotion && 'IntersectionObserver' in window) {
  revealTargets.forEach(target => target.classList.add('reveal-ready'));
  const revealObserver = new IntersectionObserver(entries => entries.forEach(entry => {
    if (entry.isIntersecting) { entry.target.classList.add('revealed'); revealObserver.unobserve(entry.target); }
  }), { threshold: .12 });
  revealTargets.forEach(target => revealObserver.observe(target));
}

document.querySelectorAll('[data-counter]').forEach(counter => {
  const end = Number(counter.dataset.counter || 0);
  if (reducedMotion || !('IntersectionObserver' in window) || end === 0) return;
  counter.textContent = '0';
  const counterObserver = new IntersectionObserver(entries => {
    if (!entries[0].isIntersecting) return;
    const started = performance.now();
    const tick = now => {
      const progress = Math.min((now - started) / 750, 1);
      counter.textContent = Math.round(end * (1 - Math.pow(1 - progress, 3))).toLocaleString('fr-FR');
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick); counterObserver.disconnect();
  }, { threshold: .7 });
  counterObserver.observe(counter);
});

const audienceContent = {
  student: ['ET','Présentez vos projets avec assurance','Publiez votre vidéo ou podcast','Ajoutez votre contenu et sa description.','Choisissez sa visibilité','Public, non répertorié ou privé.','Partagez-le à votre enseignant','Transmettez un lien sécurisé.','Créer mon espace étudiant'],
  teacher: ['EN','Retrouvez les productions au bon endroit','Recevez un lien de partage','Accédez uniquement au contenu transmis.','Consultez le travail','Vidéo, podcast et informations du projet.','Organisez vos contenus utiles','Ajoutez les productions à vos favoris.','Créer mon espace enseignant']
};
document.querySelectorAll('[data-audience]').forEach(tab => tab.addEventListener('click', () => {
  const content = audienceContent[tab.dataset.audience];
  const panel = document.querySelector('[data-audience-panel]');
  if (!content || !panel) return;
  document.querySelectorAll('[data-audience]').forEach(item => { const active=item===tab; item.classList.toggle('active',active); item.setAttribute('aria-selected',String(active)); });
  panel.classList.add('switching');
  setTimeout(() => {
    const selectors=['.demo-avatar','[data-audience-title]','[data-step-one]','[data-step-one-note]','[data-step-two]','[data-step-two-note]','[data-step-three]','[data-step-three-note]','[data-audience-action]'];
    selectors.forEach((selector,index) => { const node=panel.querySelector(selector); if(node) node.textContent=content[index]; });
    panel.classList.remove('switching');
  }, reducedMotion ? 0 : 160);
}));

const landingProgress = document.querySelector('.landing-progress span');
if (landingProgress) {
  let scrollFrame = 0;
  const updateLandingProgress = () => {
    const available = document.documentElement.scrollHeight - window.innerHeight;
    landingProgress.style.transform = `scaleX(${available > 0 ? Math.min(window.scrollY / available, 1) : 0})`;
    scrollFrame = 0;
  };
  window.addEventListener('scroll', () => { if (!scrollFrame) scrollFrame = requestAnimationFrame(updateLandingProgress); }, { passive:true });
  updateLandingProgress();
}

const heroMotion = document.querySelector('[data-hero-motion]');
if (heroMotion && !reducedMotion && window.matchMedia('(pointer:fine)').matches) {
  heroMotion.addEventListener('pointermove', event => {
    const bounds = heroMotion.getBoundingClientRect();
    heroMotion.style.setProperty('--hero-x', `${((event.clientX - bounds.left) / bounds.width - .5) * 18}px`);
    heroMotion.style.setProperty('--hero-y', `${((event.clientY - bounds.top) / bounds.height - .5) * 14}px`);
  });
  heroMotion.addEventListener('pointerleave', () => { heroMotion.style.setProperty('--hero-x','0px'); heroMotion.style.setProperty('--hero-y','0px'); });
}

if (!reducedMotion && 'IntersectionObserver' in window) {
  document.querySelectorAll('.trust-grid, .home-media-grid, .topic-grid, .process-grid, .demo-steps').forEach(group => {
    [...group.children].forEach((item,index) => { item.classList.add('stagger-item'); item.style.setProperty('--stagger-delay', `${index * 85}ms`); });
    const staggerObserver = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting) { group.classList.add('stagger-visible'); staggerObserver.disconnect(); }
    }, { threshold:.15 });
    staggerObserver.observe(group);
  });
}
