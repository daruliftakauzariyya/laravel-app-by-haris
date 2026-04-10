const CACHE_NAME = 'darul-ifta-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/assets/css/variables.css',
  '/assets/css/app-fonts.css',
  '/assets/logo/fevi.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('SW: Caching App Shell');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keyList) => {
      return Promise.all(
        keyList.map((key) => {
          if (key !== CACHE_NAME) {
            console.log('SW: Removing old cache', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  // Check if the request is for a navigation (HTML page)
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match('/'); // Return home page if offline
      })
    );
    return;
  }

  // For other assets (CSS, JS, Images)
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request).then((fetchResponse) => {
        // Cache new assets on the fly
        if (event.request.url.includes('/assets/') || event.request.url.includes('cdn')) {
           return caches.open(CACHE_NAME).then((cache) => {
             cache.put(event.request, fetchResponse.clone());
             return fetchResponse;
           });
        }
        return fetchResponse;
      });
    })
  );
});
