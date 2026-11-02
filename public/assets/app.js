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
const mainNavAnchor = document.createComment('main navigation anchor');
if (mainNav?.parentNode) mainNav.parentNode.insertBefore(mainNavAnchor, mainNav);

const placeMainNav = () => {
  if (!mainNav || !mainNavAnchor.parentNode) return;
  if (window.innerWidth <= 800) {
    if (mainNav.parentNode !== document.body) document.body.append(mainNav);
  } else if (mainNav.parentNode !== mainNavAnchor.parentNode) {
    mainNavAnchor.parentNode.insertBefore(mainNav, mainNavAnchor.nextSibling);
  }
};
placeMainNav();

menuButton?.addEventListener('click', () => {
  const open = document.body.classList.toggle('menu-visible');
  menuButton.setAttribute('aria-expanded', String(open));
  menuButton.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
});
const closeMobileMenu = () => {
  document.body.classList.remove('menu-visible');
  menuButton?.setAttribute('aria-expanded', 'false');
  menuButton?.setAttribute('aria-label', 'Ouvrir le menu');
};
mainNav?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMobileMenu));
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMobileMenu(); });
window.addEventListener('resize', () => {
  placeMainNav();
  if (window.innerWidth > 800) closeMobileMenu();
});

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
const mediaTypeSelect = document.querySelector('#mediaType');
const livePlayerWrap = document.querySelector('#livePlayerWrap');
const previewVideo = document.querySelector('#previewVideo');
const previewAudioWrap = document.querySelector('#previewAudioWrap');
const previewAudio = document.querySelector('#previewAudio');
const previewFormatBadge = document.querySelector('#previewFormatBadge');
const previewDurationBadge = document.querySelector('#previewDurationBadge');
const thumbInput = document.querySelector('#mediaThumbnail');
const thumbPreviewWrap = document.querySelector('#thumbPreviewWrap');
const thumbPreviewImg = document.querySelector('#thumbPreviewImg');
const removeThumb = document.querySelector('#removeThumb');

let currentObjectUrl = null;
let currentThumbUrl = null;

const formatSize = bytes => bytes < 1048576 ? `${(bytes / 1024).toFixed(0)} Ko` : `${(bytes / 1048576).toFixed(1)} Mo`;
const formatDuration = seconds => {
  if (!seconds || isNaN(seconds)) return '';
  const m = Math.floor(seconds / 60);
  const s = Math.floor(seconds % 60);
  return `⏱ ${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
};

// Gestion de la miniature personnalisée
thumbInput?.addEventListener('change', () => {
  const file = thumbInput.files?.[0];
  if (file && thumbPreviewImg && thumbPreviewWrap) {
    if (currentThumbUrl) URL.revokeObjectURL(currentThumbUrl);
    currentThumbUrl = URL.createObjectURL(file);
    thumbPreviewImg.src = currentThumbUrl;
    thumbPreviewWrap.hidden = false;
  }
});
removeThumb?.addEventListener('click', () => {
  if (thumbInput) thumbInput.value = '';
  if (thumbPreviewWrap) thumbPreviewWrap.hidden = true;
  if (currentThumbUrl) { URL.revokeObjectURL(currentThumbUrl); currentThumbUrl = null; }
});

const showFile = file => {
  if (!file || !preview) return;
  if (currentObjectUrl) { URL.revokeObjectURL(currentObjectUrl); currentObjectUrl = null; }

  fileName.textContent = file.name;
  fileMeta.textContent = `${formatSize(file.size)} · ${file.type || 'Fichier média'}`;
  preview.hidden = false;
  dropZone?.classList.add('has-file');

  // Détection automatique du type de média
  const isVideo = file.type.startsWith('video/') || /\.(mp4|webm|ogg|mov)$/i.test(file.name);
  const isAudio = file.type.startsWith('audio/') || /\.(mp3|wav|ogg|m4a|aac)$/i.test(file.name);

  if (isVideo && mediaTypeSelect) mediaTypeSelect.value = 'video';
  if (isAudio && mediaTypeSelect) mediaTypeSelect.value = 'podcast';

  if (previewFormatBadge) {
    previewFormatBadge.textContent = isVideo ? '🎬 Vidéo détectée' : (isAudio ? '🎙️ Podcast audio' : 'Fichier média');
  }

  // Auto-remplissage intelligent du titre s'il est vide
  if (titleInput && !titleInput.value.trim()) {
    const cleanName = file.name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ');
    titleInput.value = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
    if (titleCount) titleCount.textContent = titleInput.value.length;
  }

  // Prévisualisation multimédia en direct
  currentObjectUrl = URL.createObjectURL(file);
  if (livePlayerWrap) livePlayerWrap.hidden = false;
  if (previewDurationBadge) previewDurationBadge.hidden = true;

  if (isVideo && previewVideo) {
    previewVideo.src = currentObjectUrl;
    previewVideo.hidden = false;
    if (previewAudioWrap) previewAudioWrap.hidden = true;
    previewVideo.onloadedmetadata = () => {
      if (previewDurationBadge && previewVideo.duration) {
        previewDurationBadge.textContent = formatDuration(previewVideo.duration);
        previewDurationBadge.hidden = false;
      }
    };
  } else if (isAudio && previewAudio && previewAudioWrap) {
    previewAudio.src = currentObjectUrl;
    previewAudioWrap.hidden = false;
    if (previewVideo) previewVideo.hidden = true;
    previewAudio.onloadedmetadata = () => {
      if (previewDurationBadge && previewAudio.duration) {
        previewDurationBadge.textContent = formatDuration(previewAudio.duration);
        previewDurationBadge.hidden = false;
      }
    };
  } else {
    if (livePlayerWrap) livePlayerWrap.hidden = true;
  }
};

fileInput?.addEventListener('change', () => showFile(fileInput.files[0]));
dropZone?.addEventListener('click', event => { if (event.target !== fileInput) fileInput.click(); });
dropZone?.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') fileInput.click(); });
['dragenter','dragover'].forEach(name => dropZone?.addEventListener(name, event => { event.preventDefault(); dropZone.classList.add('dragging'); }));
['dragleave','drop'].forEach(name => dropZone?.addEventListener(name, event => { event.preventDefault(); dropZone.classList.remove('dragging'); }));
dropZone?.addEventListener('drop', event => {
  if (event.dataTransfer.files.length) {
    fileInput.files = event.dataTransfer.files;
    showFile(fileInput.files[0]);
  }
});
removeFile?.addEventListener('click', () => {
  if (fileInput) fileInput.value = '';
  if (preview) preview.hidden = true;
  dropZone?.classList.remove('has-file');
  if (previewVideo) { previewVideo.pause(); previewVideo.src = ''; previewVideo.hidden = true; }
  if (previewAudio) { previewAudio.pause(); previewAudio.src = ''; }
  if (previewAudioWrap) previewAudioWrap.hidden = true;
  if (livePlayerWrap) livePlayerWrap.hidden = true;
  if (currentObjectUrl) { URL.revokeObjectURL(currentObjectUrl); currentObjectUrl = null; }
});

// Modal de progression & délai de traitement/encodage
const uploadForm = document.querySelector('#uploadForm');
const publishButton = document.querySelector('#publishButton');
const uploadModal = document.querySelector('#uploadModal');
const modalFormatLabel = document.querySelector('#modalFormatLabel');
const modalMediaTitle = document.querySelector('#modalMediaTitle');
const uploadBarFill = document.querySelector('#uploadBarFill');
const uploadPercentage = document.querySelector('#uploadPercentage');
const uploadSizeInfo = document.querySelector('#uploadSizeInfo');
const stepUpload = document.querySelector('#stepUpload');
const stepUploadTag = document.querySelector('#stepUploadTag');
const stepUploadCaption = document.querySelector('#stepUploadCaption');
const stepProcess = document.querySelector('#stepProcess');
const stepProcessTag = document.querySelector('#stepProcessTag');
const stepProcessCaption = document.querySelector('#stepProcessCaption');
const stepFinalize = document.querySelector('#stepFinalize');
const stepFinalizeTag = document.querySelector('#stepFinalizeTag');
const stepFinalizeCaption = document.querySelector('#stepFinalizeCaption');
const modalErrorBox = document.querySelector('#modalErrorBox');
const modalErrorMessage = document.querySelector('#modalErrorMessage');
const modalRetryBtn = document.querySelector('#modalRetryBtn');
const modalFooterNotice = document.querySelector('#modalFooterNotice');

modalRetryBtn?.addEventListener('click', () => {
  if (uploadModal) uploadModal.hidden = true;
  if (publishButton) { publishButton.disabled = false; publishButton.classList.remove('loading'); }
});

uploadForm?.addEventListener('submit', event => {
  if (!uploadModal) return;

  const file = fileInput?.files?.[0];
  if (!file) {
    alert('Veuillez sélectionner un fichier vidéo ou podcast à publier.');
    event.preventDefault();
    return;
  }

  event.preventDefault();

  uploadModal.hidden = false;
  if (modalErrorBox) modalErrorBox.hidden = true;
  if (modalFooterNotice) modalFooterNotice.hidden = false;
  if (publishButton) { publishButton.disabled = true; publishButton.classList.add('loading'); }

  const isVideo = (mediaTypeSelect?.value === 'video');
  if (modalFormatLabel) modalFormatLabel.textContent = isVideo ? 'Mise en ligne Vidéo HD' : 'Mise en ligne Podcast Audio';
  if (modalMediaTitle) modalMediaTitle.textContent = titleInput?.value || file.name;

  uploadBarFill.style.width = '0%';
  uploadPercentage.textContent = '0%';
  uploadSizeInfo.textContent = `0 Mo / ${formatSize(file.size)}`;

  stepUpload.className = 'stepper-step active';
  stepUploadTag.textContent = 'En cours';
  stepUploadTag.className = 'step-status-tag';
  stepUploadCaption.textContent = 'Transfert sécurisé vers le serveur…';

  stepProcess.className = 'stepper-step';
  stepProcessTag.textContent = 'En attente';
  stepFinalize.className = 'stepper-step';
  stepFinalizeTag.textContent = 'En attente';

  const formData = new FormData(uploadForm);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', uploadForm.action || window.location.href, true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  xhr.upload.addEventListener('progress', e => {
    if (e.lengthComputable) {
      const percent = Math.min(Math.round((e.loaded / e.total) * 100), 100);
      uploadBarFill.style.width = `${percent}%`;
      uploadPercentage.textContent = `${percent}%`;
      uploadSizeInfo.textContent = `${formatSize(e.loaded)} / ${formatSize(e.total)}`;
    }
  });

  xhr.addEventListener('load', () => {
    if (xhr.status >= 200 && xhr.status < 300) {
      uploadBarFill.style.width = '100%';
      uploadPercentage.textContent = '100%';
      stepUpload.className = 'stepper-step done';
      stepUploadTag.textContent = '✓ Terminé';
      stepUploadCaption.textContent = `${formatSize(file.size)} transférés avec succès.`;

      // Début du délai réaliste de traitement et encodage multimédia
      stepProcess.className = 'stepper-step active';
      stepProcessTag.textContent = 'Traitement';
      stepProcessCaption.textContent = isVideo ? 'Encodage vidéo et vérification du débit…' : 'Normalisation audio et calcul des pistes…';

      setTimeout(() => {
        stepProcess.className = 'stepper-step done';
        stepProcessTag.textContent = '✓ Optimisé';
        stepProcessCaption.textContent = 'Pistes multimédias prêtes pour le streaming.';

        stepFinalize.className = 'stepper-step active';
        stepFinalizeTag.textContent = 'Finalisation';
        stepFinalizeCaption.textContent = 'Enregistrement et génération des accès…';

        setTimeout(() => {
          stepFinalize.className = 'stepper-step done';
          stepFinalizeTag.textContent = '✓ Publié';
          stepFinalizeCaption.textContent = 'Votre contenu est maintenant disponible !';
          if (modalFormatLabel) modalFormatLabel.textContent = '🎉 Succès';

          setTimeout(() => {
            window.location.href = '?page=my-media';
          }, 950);
        }, 1100);
      }, 1600);

    } else {
      let errorMsg = 'Une erreur est survenue lors de la mise en ligne.';
      try {
        const json = JSON.parse(xhr.responseText);
        if (json.error) errorMsg = json.error;
      } catch (err) {}
      if (modalErrorBox && modalErrorMessage) {
        modalErrorMessage.textContent = errorMsg;
        modalErrorBox.hidden = false;
      }
      if (modalFooterNotice) modalFooterNotice.hidden = true;
      stepUpload.className = 'stepper-step';
      stepUploadTag.textContent = 'Erreur';
    }
  });

  xhr.addEventListener('error', () => {
    if (modalErrorBox && modalErrorMessage) {
      modalErrorMessage.textContent = 'Erreur réseau. Veuillez vérifier votre connexion et réessayer.';
      modalErrorBox.hidden = false;
    }
    if (modalFooterNotice) modalFooterNotice.hidden = true;
  });

  xhr.send(formData);
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

// Basculement de visibilité des mots de passe (Afficher / Masquer)
document.addEventListener('click', event => {
  const toggleBtn = event.target.closest('.password-toggle-btn');
  if (!toggleBtn) return;

  const wrap = toggleBtn.closest('.password-field-wrap');
  const input = wrap?.querySelector('input');
  if (!input) return;

  const eyeShow = toggleBtn.querySelector('.eye-show');
  const eyeHide = toggleBtn.querySelector('.eye-hide');

  if (input.type === 'password') {
    input.type = 'text';
    if (eyeShow) eyeShow.hidden = true;
    if (eyeHide) eyeHide.hidden = false;
    toggleBtn.setAttribute('aria-label', 'Masquer le mot de passe');
    toggleBtn.setAttribute('title', 'Masquer le mot de passe');
  } else {
    input.type = 'password';
    if (eyeShow) eyeShow.hidden = false;
    if (eyeHide) eyeHide.hidden = true;
    toggleBtn.setAttribute('aria-label', 'Afficher le mot de passe');
    toggleBtn.setAttribute('title', 'Afficher le mot de passe');
  }

  input.focus();
});

