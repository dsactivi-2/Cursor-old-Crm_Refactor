<div class="idk_contact_box idk_margin_top200">
    <div class="row">
        <div class="col-lg-5">
            <div id="map"></div>

            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('map').setView([45.445, 13], 5); // Set initial view

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  maxZoom: 19,
                }).addTo(map);

                var locations = {
                  bihac: [44.7776929, 17.1941385],
                  jelah: [44.6618039, 17.9774096],
                  sarajevo: [43.855231, 18.4148159],
                  regensburg: [49.0128436, 12.0715657],
                  steinhausen: [47.1870475, 8.4842688],
                  novi_beograd: [44.8224338, 20.4132368]
                };

                var markers = {};
                for (var key in locations) {
                  markers[key] = L.marker(locations[key]).addTo(map);
                }

                markers['bihac'].bindPopup('<h4>Banja Luka</h4><p>Prvog krajiškog korpusa broj 35, 78000 Banja Luka</p>');
                markers['jelah'].bindPopup('<h4>Jelah</h4><p>Huseina Kapetanovića Gradaščevića 54, 74264 Jelah</p>');
                markers['sarajevo'].bindPopup('<h4>Sarajevo</h4><p>Skenderija 8, 71000 Sarajevo</p>');
                markers['regensburg'].bindPopup('<h4>Regensburg</h4><p>Johanna-Kinkel Straße 1-2, 93049 Regensburg</p>');
                markers['steinhausen'].bindPopup('<h4>Steinhausen</h4><p>Sumpfstrasse 26, 6312 Steinhausen</p>');
                markers['novi_beograd'].bindPopup('<h4>Novi Beograd</h4><p>Bulevar Mihajla Pupina 165G, 11070 Novi Beograd</p>');

                function zoomToLocation(location) {
                  map.setView(locations[location], 13); // Zoom level 13
                  markers[location].openPopup();
                }

                document.getElementById('bihac').addEventListener('click', function() {
                  zoomToLocation('bihac');
                });
                document.getElementById('jelah').addEventListener('click', function() {
                  zoomToLocation('jelah');
                });
                document.getElementById('sarajevo').addEventListener('click', function() {
                  zoomToLocation('sarajevo');
                });
                document.getElementById('regensburg').addEventListener('click', function() {
                  zoomToLocation('regensburg');
                });
                document.getElementById('steinhausen').addEventListener('click', function() {
                  zoomToLocation('steinhausen');
                });
                document.getElementById('novi_beograd').addEventListener('click', function() {
                  zoomToLocation('novi_beograd');
                });
              });
            </script>
        </div>
        <?php getContent(47); ?>
        <?php echo $content_lang_content; ?>
    </div>
</div>
