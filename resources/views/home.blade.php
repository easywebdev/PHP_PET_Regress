@extends('layout')

@section('content')

    <header class="header">
        <div class="container title">
            <h1 class="h1">Regression Calculator</h1>
        </div>
    </header>

    <main class="main">
        <div class="container block mb-1rem">
            <div class="block__item mw-7rem h-18rem">
                <h2 class="h2">Input data</h2>
                <table id="inputtbl" class="tbl">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>X</th>
                        <th>Y</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 0; $i < count($demoData); $i++)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td contenteditable="true">{{ $demoData[$i][0] }}</td>
                            <td contenteditable="true">{{ $demoData[$i][1] }}</td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>

            <div class="block__item mw-7rem h-18rem" id="result">
                <div id="regress"></div>
            </div>
        </div>

        <div class="container">
            <div class="block">
                <div class="block__item mw-7rem">
                    <a class="btn" href="javascript:addRow()">+</a>
                    <a class="btn" href="javascript:removeLastRow()">-</a>
                    <a class="btn" href="javascript:removeEmptyRows()">Clear empty</a>
                </div>
                <div class="block__item mw-7rem">
                    <a class="btn" href="javascript:buildRegress('/linear')">Linear</a>
                    <a class="btn" href="javascript:buildRegress('/polynomial')">Polynomial</a>
                </div>
            </div>
        </div>


    </main>

    <section class="chart-area">
        <div class="container chart">
            <canvas id="myChart"></canvas>
        </div>
    </section>




@endsection

@push('scripts')
    <script src="{{asset('js/build_chart.js')}}" type="application/javascript"></script>
    <script src="{{asset('js/home.js')}}" type="application/javascript"></script>
@endpush

