<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p><strong>Nama Pegawai: </strong>{{Auth::user()->name}}</p>
                        <p><strong>Kantor : </strong>{{$schedule->office->name}}</p>
                        <p><strong>Shift : </strong>{{$schedule->shift->name}} ({{$schedule->shift->start_time}} - {{$schedule->shift->end_time}})</p>
                        @if($schedule->is_wfa)
                            <p><strong>Status : </strong>WFA</p>
                        @else
                            <p><strong>Status : </strong>WFO</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Waktu Masuk</h4>
                            <p><strong>{{$attendance ? $attendance->start_time : '-'}}</strong></p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
                            <p><strong>{{$attendance ? $attendance->end_time : '-' }}</strong></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore></div>
                    @if (session()->has('error'))
                        <div style="color:red; padding:10px; border:1px solid red; background-color: #fdd;">
                            {{session('error')}}
                        </div>
                    @endif
                    <form action="" class="row g-3 mt-3" wire:submit="store" enctype="multipart/form-data">
                        <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded" onclick="tagLocation()">Tag Location</button>
                        @if($insideRadius)
                        <button class="px-4 py-2 bg-green-500 text-white rounded" type="submit" onclick="tagLocation()">Submit Presensi</button>
                        @endif
                        <a href="/admin/attendances"><button type="button" class="mt-3 px-4 py-2 bg-red-500 text-white rounded">Cancel</button></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let lat;
        let lng;
        let component;
        let marker;
        const office = [{{$schedule->office->latitude}}, {{$schedule->office->longitude}}];
        const radius = {{$schedule->office->radius}};
        document.addEventListener('livewire:initialized', function(){
            component = @this;
            map = L.map('map').setView([{{$schedule->office->latitude}}, {{$schedule->office->longitude}}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
            const circle = L.circle(office, {
                color:'red',
                fillColor:'#f03',
                fillOpacity:0.5,
                radius:radius
            }).addTo(map);
        })

        function tagLocation(){
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function (position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;

                    if(marker){
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 13);

                    if(isWithinRadius(lat, lng, office, radius)){
                        component.set('insideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                    } else{
                        alert('Anda diluar Radius');
                    }
                })
            }else{
                alert('Lokasi tidak ditemukan');
            }
        }

        function isWithinRadius(lat, lng, office, radius){
            const is_wfa = {{$schedule->is_wfa}}
            if(is_wfa){
                return true;
            } else{
                let distance = map.distance([lat, lng], office);
                return distance <= radius;
            }
        }
    </script>
</div>
