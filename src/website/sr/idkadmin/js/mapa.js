var atrakcije = new L.LayerGroup();
var desavanja = new L.LayerGroup();
var dogafest = new L.LayerGroup();
var jelopice = new L.LayerGroup();
var korisneinfo = new L.LayerGroup();
var kultura = new L.LayerGroup();
var kupovina = new L.LayerGroup();
var multimedija = new L.LayerGroup();
var okolica = new L.LayerGroup();
var smjestaj = new L.LayerGroup();
var sport = new L.LayerGroup();
var zabava = new L.LayerGroup();
   
var map = L.map('map', {
	center: [44.81374, 15.8702],
	zoom: 9,
	fullscreenControl: true,
	layers: [atrakcije, desavanja, dogafest, jelopice, korisneinfo, kultura, kupovina, multimedija, okolica, smjestaj, sport, zabava]
});
      
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
	maxZoom: 18,
	attribution: '© <a href="http://www.visitbihac.com">VisitBihac</a>',
	id: 'examples.map-i875mjb7'
}).addTo(map);

var LeafIcon = L.Icon.extend({
	options: {
		shadowUrl: 'images/mapa/marker-shadow.png',
		iconSize:     [32, 37],
		shadowSize:   [32, 37],
		iconAnchor:   [18, 37],
		shadowAnchor: [10, 37],
		popupAnchor:  [-3, -37]
	}
});

var baseLayers = {};
      
var overlays = {
	"Atrakcije": atrakcije,
	"Dešavanja": desavanja,
	"Događanja i festivali": dogafest,
	"Jelo i piće": jelopice,
	"Korisne informacije": korisneinfo,
	"Kultura": kultura,
	"Kupovina": kupovina,
	"Multimedija": multimedija,
	"Okolica": okolica,
	"Smještaj": smjestaj,
	"Sport i rekreacija": sport,
	"Zabava": zabava
};
      
L.control.layers(baseLayers, overlays).addTo(map);

var popup = L.popup();

//Lokacija korisnika
lc = L.control.locate({
	follow: true
}).addTo(map);

map.on('startfollowing', function() {
	map.on('dragstart', lc.stopFollowing);
}).on('stopfollowing', function() {
	map.off('dragstart', lc.stopFollowing);
});