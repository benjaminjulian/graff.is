var map = L.map('map', {
    center: [20.0, 5.0],
    minZoom: 2,
    zoom: 2,
  })
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    subdomains: ['a', 'b', 'c'],
  }).addTo(map)
  
  var myURL = jQuery('script[src$="mapcode.js"]')
    .attr('src')
    .replace('mapcode.js', '')
  
  var myIcon = L.icon({
    iconUrl: myURL + 'marker.png',
    iconRetinaUrl: myURL + 'markerxl.png',
    iconSize: [18, 30],
    iconAnchor: [9, 5],
    popupAnchor: [0, -100],
  })
  
  for (var i = 0; i < markers.length; ++i) {
    L.marker([markers[i].lat, markers[i].lng], { icon: myIcon })
      .bindPopup(
        '<img src="https://graff.s3.eu-west-1.amazonaws.com/thumbs/' + markers[i].file_name + '">'
      )
      .addTo(map)
  }