<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{ env('APP_NAME') }} - Radni izvještaji</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/css/print.css') }}">
    </head>

    <body>
        @foreach ($result as $key => $statistics)
        <div class="report-wrapper">
            @php
                $i = 1;
                $total_work_time_in_month = 0;
            @endphp
            <header class="mb-5">
                <div class="container-fluid">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-6">
                            <img style="width: 250px; margin-left: -15px" src="{{ asset('assets/images/logo/pdf-logo.png') }}" alt="">
                        </div>
                        <div class="col-md-6 text-right">
                            Vlada USK
                        </div>
                    </div>
                </div>
            </header>

            <div class="subheader container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="subheader__top text-center">
                            <h4>Evidencija o radnom vremenu za mjesec <u><span>{{ $month }}</span></u> {{ $year }}. godine</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="subheader__bottom">
                            Ime i prezime radnika: <u>{{ $key}}</u>
                        </div>
                    </div>
                </div>
            </div>

            <section>
                <div class="container-fluid">
                    <div class="table-container">
                        <div class="table-header">
                            <div>Red br</div>
                            <div>Datum u <br>mjesecu</div>
                            <div>Početak rada</div>
                            <div>Završetak rada</div>
                            <div>Prekid rada</div>
                            <div>Ukupno <br>dnevno radno <br>vrijeme</div>
                            <div>Vrijeme <br>terenskog rada</div>
                            <div>Vrijeme <br>pripravnosti</div>
                            <div>Vrijeme <br>neprisustva na <br>poslu</div>
                            <div>Ostali podaci o<br> radnom vremenu</div>
                            <div>
                                <strong>Ukupno radnih <br>sati</strong>
                            </div>
                        </div>
                        <div class="table-subheader">
                            <div>1</div>
                            <div>2</div>
                            <div>3</div>
                            <div>4</div>
                            <div>5</div>
                            <div>6</div>
                            <div>7</div>
                            <div>8</div>
                            <div>9</div>
                            <div>10</div>
                            <div>
                                <strong>11</strong>
                            </div>
                        </div>

                        @foreach ($statistics as $key => $statistic)
                            <div class="table-data">
                                <div>{{ $i++ }}</div>
                                <div>{{ $statistic['date'] }}</div>
                                <div>{{ $statistic['start'] }}</div>
                                <div>{{ $statistic['end'] }}</div>
                                <div>{{ $statistic['break'] }}</div>
                                <div>{{ $statistic['total_daily_work_time'] }}</div>
                                <div>{{ $statistic['field'] }}</div>
                                <div>{{ $statistic['standby'] }}</div>
                                <div>{{ $statistic['absence'] }}</div>
                                <div>{{ $statistic['other'] }}</div>
                                <div>{{ $statistic['total_work_time_in_day'] }}</div>

                                @if ($statistic['total_daily_work_time'] != '-')
                                    @php
                                        $total_work_time_in_month+= $statistic['total_work_time_in_day_in_mins'];
                                    @endphp
                                @else
                                    @php
                                        $total_work_time_in_month+= 0;
                                    @endphp
                                @endif


                            </div>
                        @endforeach

                        <div class="table-footer">
                            <div>
                                <strong>UKUPNO RADNIH SATI U MJESECU</strong>
                            </div>
                            <div>
                                {{ round($total_work_time_in_month / 60) }}
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        @endforeach
    </body>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</html>
