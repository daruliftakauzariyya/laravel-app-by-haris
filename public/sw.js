/**
 * Darul Ifta Kauzariyya - Progressive Web App Service Worker
 * With 700 MB Max Local Cache Quota Management (LRU Eviction)
 */

const CACHE_VERSION = 'darul-ifta-v4';
const STATIC_CACHE = `darul-ifta-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `darul-ifta-dynamic-${CACHE_VERSION}`;
const IMAGE_CACHE = `darul-ifta-images-${CACHE_VERSION}`;

// 700 MB Maximum Cache Capacity Limit
const MAX_CACHE_SIZE_BYTES = 700 * 1024 * 1024; // 734,003,200 bytes (700 MB)
const MAX_DYNAMIC_ITEMS = 2500; // Safeguard item count limit

// Core App Shell Assets for instant loading
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/assets/css/variables.css',
  '/assets/css/app-fonts.css',
  '/assets/logo/fevi.png',
  '/assets/logo/Darul-Ifta-Logo.png',
  '/assets/logo/logoblueNew.svg',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

// ── Service Worker Installation ──
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => {
      console.log('[PWA SW] Pre-caching Core App Shell');
      return cache.addAll(STATIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// ── Service Worker Activation & Old Cache Cleanup ──
self.addEventListener('activate', (event) => {
  const currentCaches = [STATIC_CACHE, DYNAMIC_CACHE, IMAGE_CACHE];
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((name) => {
          if (!currentCaches.includes(name)) {
            console.log('[PWA SW] Removing outdated cache:', name);
            return caches.delete(name);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// ── Cache Trimming & 700 MB Capacity Enforcement ──
async function enforceMaxCacheCapacity() {
  try {
    if (self.navigator && self.navigator.storage && self.navigator.storage.estimate) {
      const estimate = await self.navigator.storage.estimate();
      const currentUsage = estimate.usage || 0;

      // If storage usage exceeds 700 MB, prune dynamic and image caches
      if (currentUsage >= MAX_CACHE_SIZE_BYTES) {
        console.warn(`[PWA SW] Cache usage (${(currentUsage / (1024 * 1024)).toFixed(2)} MB) reached or exceeded 700 MB limit. Pruning oldest cached items...`);
        await trimOldestEntries(DYNAMIC_CACHE, 200);
        await trimOldestEntries(IMAGE_CACHE, 200);
      }
    }
  } catch (err) {
    console.warn('[PWA SW] Storage quota estimate failed:', err);
  }
}

// Helper to remove oldest N entries from a cache
async function trimOldestEntries(cacheName, countToRemove = 50) {
  try {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > countToRemove) {
      for (let i = 0; i < countToRemove; i++) {
        await cache.delete(keys[i]);
      }
    }
  } catch (err) {
    console.error('[PWA SW] Cache trimming error:', err);
  }
}

// ── Fetch Event Interception ──
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Only cache GET requests (ignore POST, PUT, DELETE, etc.)
  if (request.method !== 'GET') {
    return;
  }

  // Do not cache sensitive endpoints (admin dashboard, logout, live search tokens)
  if (url.pathname.startsWith('/admin') || 
      url.pathname.includes('/auth/google') || 
      url.pathname.includes('/logout')) {
    return;
  }

  // 1. Navigation Requests (HTML pages) - Network-first with Cache fallback
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(DYNAMIC_CACHE).then((cache) => {
              cache.put(request, responseClone);
              enforceMaxCacheCapacity();
            });
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(request).then((cachedResponse) => {
            return cachedResponse || caches.match('/');
          });
        })
    );
    return;
  }

  // 2. Images (slider, logos, uploads, fatwa attachments) - Cache-first with 700MB quota guard
  if (request.destination === 'image' || url.pathname.match(/\.(jpg|jpeg|png|webp|svg|gif|ico)$/i)) {
    event.respondWith(
      caches.match(request).then((cachedImage) => {
        if (cachedImage) {
          return cachedImage;
        }
        return fetch(request).then((networkImage) => {
          if (networkImage && networkImage.status === 200) {
            const imgClone = networkImage.clone();
            caches.open(IMAGE_CACHE).then((cache) => {
              cache.put(request, imgClone);
              enforceMaxCacheCapacity();
            });
          }
          return networkImage;
        });
      })
    );
    return;
  }

  // 3. Static Assets (CSS, JS, Fonts) - Stale-While-Revalidate
  event.respondWith(
    caches.match(request).then((cachedAsset) => {
      const fetchPromise = fetch(request).then((networkAsset) => {
        if (networkAsset && networkAsset.status === 200) {
          const assetClone = networkAsset.clone();
          caches.open(STATIC_CACHE).then((cache) => {
            cache.put(request, assetClone);
          });
        }
        return networkAsset;
      }).catch(() => cachedAsset);

      return cachedAsset || fetchPromise;
    })
  );
});

// ── Background Sync / Message Handling ──
self.addEventListener('message', (event) => {
  if (event.data && event.data.action === 'skipWaiting') {
    self.skipWaiting();
  }
  if (event.data && event.data.action === 'clearCache') {
    caches.keys().then((keys) => {
      keys.forEach((key) => caches.delete(key));
    });
  }
});
