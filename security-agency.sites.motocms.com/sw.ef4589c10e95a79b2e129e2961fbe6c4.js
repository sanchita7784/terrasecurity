/* eslint no-var: off */
/* eslint no-console: off */
/* eslint no-redeclare: off */
/* global fetch createImageBitmap self caches*/
var hostReplacer = {}
function checkReplacer() {
  return fetch("/.sw_/_host_/_replacer_?" + Date.now())
    .then(function(res) {return res.json()})
    .then(function(r) {hostReplacer = r})
    .catch(function(error) {console.error(error)})
}
setInterval(checkReplacer, 60000)

function replaceHost(host) {
  var arr = host.split(":")
  for (var key in hostReplacer) {
    if (key == arr[0]) return hostReplacer[key] + (arr[1] ? ":" + arr[1] : "")
  }
  return host
}

var supportsWebp = false

function noCache(resp) {
  resp.__nocache = true
  return resp
}

function fetchOptimized(url, host) {
  var matches = (url.protocol + "//" + host + url.pathname).match(
    /^(https?:\/\/[^/]+\/res\/[\da-z]+\/[\da-f]{24}_optimized)(?:[^./]+)(\..+)?$/
  )
  if (!matches) return fetch(url.protocol + "//" + host + url.pathname + url.search)

  return fetch(url.protocol + "//" + host + url.pathname + url.search)
    .catch(function() {return {status: 404}})
    .then(function(resp) {
      if (resp.status == 404 || resp.status == 403) {
        return fetch(matches[1] + (matches[2] || '')).then(function(resp) {
          if (resp.status == 404 || resp.status == 403) {
            return fetch(matches[1])
          }
          return resp
        }).then(noCache)
      }
      return resp
    })
}

var matchWebpApi = /^https?:\/\/[^/]+\/res\/[\da-z]+\/[\da-f]{24}[^/]*$/

function fetchWithCache(event, _fetch) {
  event.respondWith(
    caches.match(event.request, {ignoreSearch: true}).then(function(result) {
      if (result && result.ok) return result
      return _fetch.then(function(resp) {
        if (resp.status == 200 && !resp.__nocache) {
          return caches.open("webp-cache").then(function(cache) {
            cache.put(event.request, resp.clone())
            return resp
          })
        }
        return resp
      })
    })
  )
}

function processResource(event, url, host) {
  if (event.request.method != "GET") return

  if (url.search.indexOf("nowebp") >= 0) {
    return event.respondWith(fetchOptimized(url, host))
  }
  if (!supportsWebp || url.pathname.match(/\.webp$/)) {
    return fetchWithCache(event, fetchOptimized(url, host))
  }

  return fetchWithCache(event, fetch(
    url.protocol + "//" + host + url.pathname + ".webp" + url.search,
  ).catch(function(error) {
    console.error(error)
    return {status: 666}
  }).then(function(resp) {
    if (resp.status == 200) return resp
    if (resp.status == 404) {
      var ref = encodeURIComponent(url.pathname.slice(1))
      if ((url.protocol + "//" + host + url.pathname).match(matchWebpApi)) {
        fetch(
          url.protocol + "//api.weblium.com/api/resource/webp/check?ref=" + ref,
          {method: "POST", redirect: 'follow'}
        ).catch(function(error) {console.error(error)})
      }
    }
    return fetchOptimized(url, host).then(noCache)
  }))
}

var matchWebp = /^https?:\/\/[^/]+\/(?:res\/[\da-z]+\/[\da-f]{24}[^/]*|[^/]+\/res\/[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}(?:\/[^/]+)?)$/

self.addEventListener("fetch", function(event) {
  var url = new URL(event.request.url)
  var host = replaceHost(url.host)
  if (!(url.protocol + "//" + url.host + url.pathname).match(matchWebp)) {
    if (host != url.host && event.request.method != "GET") {
      return fetch(url.protocol + "//" + host + url.pathname + url.search)
    }
    return
  }
  return processResource(event, url, host)
})

self.addEventListener("install", function(event) {
  if (!this.createImageBitmap) {
    return event.waitUntil(self.skipWaiting())
  }
  var webpData = "data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA="
  event.waitUntil(
    checkReplacer().then(function() {
      return fetch(webpData).then(function(r) {return r.blob()})
    }).then(function(blob) {
      return createImageBitmap(blob).then(function() {supportsWebp = true})
    }).catch(function() {}).then(function() {self.skipWaiting()}))
})

// self.addEventListener("activate", function() {
//   clients.claim().then(function() {
//     clients.matchAll().then(function(clients) {
//       clients.forEach(function(client) {
//         client.postMessage("active")
//       })
//     })
//   })
// })

