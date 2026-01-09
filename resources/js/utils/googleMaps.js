export function waitForGoogleMaps() {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.maps && window.google.maps.places) {
      resolve()
      return
    }

    const interval = setInterval(() => {
      if (window.google && window.google.maps && window.google.maps.places) {
        clearInterval(interval)
        resolve()
      }
    }, 100)

    // seguridad: no esperar infinito
    setTimeout(() => {
      clearInterval(interval)
      reject(new Error('Google Maps no cargó'))
    }, 10000)
  })
}
