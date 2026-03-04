<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- icon -->
    <link rel="icon" type="image/png" href="{{ asset('onepage-blue.png') }}">
    <title>OnePage</title>
    <link rel="stylesheet" href="{{ public_path('build/assets/app.css') }}">

    <style>
        @page {
            margin: 50px 65px 50px 65px; /* top, right, bottom, left */
        }
        body {
            font-family: Helvetica, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding-top: 1px;   /* same as header height */
            padding-bottom: 120px; /* same as footer height + signatory table height */
            /* border: 3px solid red; */
        }
        header {
            position: fixed;
            top: 0; /* adjust depending on your @page top margin */
            left: 0;
            right: 0;
            height: 170px;
            /* Optional styling */
            text-align: center;
        }
        .connector { 
            background-image: url('{{ $connector }}');
            margin-right: auto;
            margin-left: auto;
            width:45px;
            height:45px;
            background-size: 45px 45px;
            background-repeat:no-repeat;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14pt;
            padding-top: 5px;
        }
        footer {
            position: fixed;
            bottom: -50px; /* adjust depending on your @page bottom margin */
            left: 0;
            right: 0;
            height: 40px;
            /* border: 3px solid blue; */

            /* Optional styling */
            text-align: center;
            font-size: 8pt;
        }
        .break{
            page-break-before: always;
        }
        table{
            border-collapse: collapse;
            border: 1px solid black;
            width: 100%;
            color: #000;
        }
        .box {
            width: 300px;
            margin: 40px auto;
            padding: 20px;
            border: 2px solid #2563eb;
            border-radius: 12px;
        }
        #header_table tbody tr td,
        #scope_objectives_table tbody tr td,
        #signatory_table tbody tr td,
        #interfaces_table tbody tr td, {
            border: 1px solid black;
            padding-left: 10px;
            padding-right: 5px;
        }
        .label_cell, .data_cell{
            width: 20%;
        }
        #manual_name,
        #process_table thead tr{
            font-weight: 700;
            font-size: 10pt;
            text-align: center;
            color: {{ $text_color }};
            background-color: {{ $color}};
        }
        #title-row{
            font-weight: 700;
            font-size: 14pt;
            text-align: center;
            text-transform: uppercase;
        }
        #scope_objectives_table tbody tr td:nth-child(2){
            width: 80%;
        }
        #scope_objectives_table ul{
            padding-left: 10px;
            margin: 0;
        }
        #process_table tbody{
            font-size: 10pt;
            text-align: center;
        }
        .arrow-line {
            width: 0;
            height: 12px;
            border-left: 3px solid black;
            border-right: 3px solid black;
            border-top: 3px solid #000;
            margin: 5px auto;
        }
        .arrow-down {
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #000;
            margin: 5px auto;
        }
        #process_table tbody tr td{
            padding-top: 0;
            padding-bottom: 0;
            padding-left: 5px;
            padding-right: 5px;
            margin-top: 0;
            margin-bottom: 0;
        }
        #process_table tbody tr:first-child td{
            padding-top: 10px
        }
        #process_table tbody tr:last-child td:nth-child(2){
            padding-bottom: 10px
        }
        
        .signatory {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        #signatory_table {
            font-size: 10pt;
            page-break-inside: avoid;
            width: 100%;
        }
        #signatory_table tr:nth-child(2) td{
            text-align: center;
        }
        #scope_objectives_table p{
            padding: 0;
            margin: 0;
        }
        td ol{
            padding-left: 1rem;
        }
        .widowed{
            orphans:3;
            widows:3;
        }
        .note-title {
            font-weight: 700;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }

.page-number:after {
    content: counter(page);
}

.total-pages:after {
    content: counter(pages);
}
    </style>
</head>
<body style="height: 100%; vertical-align: top;">
    <header>
        <table id="header_table">
            <tbody>
                <tr id="manual_name" style="width:60%">
                    <td colspan="3">SYSTEM PROCEDURES MANUAL</td>
                </tr>
                <tr>
                    <td rowspan="5" style="font-size:14pt; text-align:center">
                        <div style="width:100%; height: 90px; text-align:center; vertical-align: center">
                            <img src="{{ $logo }}" style="height: 100%;" >
                        </div>
                    </td>
                    <td class="label_cell">Section No.:</td>
                    <td class="data_cell">{{ $doc->section->section_number }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Revision No.:</td>
                    <td class="data_cell">{{ $doc->revision_number ?? "-" }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Document No.:</td>
                    <td class="data_cell">{{ $doc->code }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Effective Date:</td>
                    <td class="data_cell">{{ $doc->effective_date ? date("m/d/Y", strtotime($doc->effective_date)) : "-"}}</td>
                </tr>
                <tr>
                    <td class="label_cell">Page Number:</td>
                    <td class="data_cell"></td>
                </tr>
                <tr id="title-row">
                    <td colspan="3">{{ $doc->title }}</td>
                </tr>
            </tbody>
        </table>
        <span style="font-size: 9pt; font-style: italic;"><strong>STRICTLY CONFIDENTIAL</strong> - For use of FCU Solutions Inc only. Unauthorized reproduction is strictly prohibited.</span>
    </header>

    <div class="pdf-footer">
        Footer content here
    </div>
    <main style="height: 100%; ">
        <div class="content">
            <table id="scope_objectives_table">
                <tbody>
                    <tr>
                        <td><strong>OBJECTIVE/S</strong></td>
                        <td style="text-align: justify;">{!! $doc->objective !!}</td>
                    </tr>
                    <tr>
                        <td><strong>SCOPE</strong></td>
                        <td style="text-align: justify;">{!! $doc->scope !!}</td>
                    </tr>
                </tbody>
            </table>

            @php
                $note = 1;
            @endphp

            <table id="process_table" style="margin-top: 15px; margin-bottom: 8px;">
                <thead>
                    <tr>
                        <th>Responsibility</th>
                        <th style="width: 40%;">Activities</th>
                        <th></th>
                        <th>Interfaces</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="height:5px;"></td>
                    </tr>
                    @foreach ($steps as $key => $step)
                    @if ($key !== 0 && $key % 5 === 0)
                        {{-- connector before breaking --}}
                        <tr style="border-bottom: 1px solid black;">
                            <td></td>
                            <td style="text-align:center;">
                                <div class="connector">
                                    A
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>

                        {{-- force page break --}}
                        <tr style="page-break-before: always;"></tr>

                        {{-- connector at top of new page --}}
                        <tr>
                            <td></td>
                            <td style="text-align:center;">
                                <div class="connector">
                                    A
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div class="arrow-line" style="margin-top:0; margin-bottom: 0"></div>
                                <div class="arrow-down" style="margin-top:0; margin-bottom: 0"></div>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                    <tr>
                        <td style="width:120px;">{{$step->responsibility}} </td>
                        <td style="padding-top: 5px; padding-bottom: 5px; width:200px; border: 1px solid black;">
                            {{$step->activities}}
                        </td>
                        <td style="padding-left:5px; width:25px; padding-right:5px;">
                            @if ($step->note)
                                Note {{$note++}}
                            @endif
                        </td>
                        <td style="width: 30%; padding-left:5px; padding-right:5px;">
                            @foreach($step->interfaces as $interface)
                                {{$interface->title}} <br>
                            @endforeach
                        </td>
                    </tr>
                    @if($key+1 !== count($steps))
                    <tr>
                        <td></td>
                        <td>
                            <div class="arrow-line" style="margin-top:0; margin-bottom: 0"></div>
                            <div class="arrow-down" style="margin-top:0; margin-bottom: 0"></div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                    @endforeach
                    <tr>
                        <td colspan="4" style="height:10px;"></td>
                    </tr>
                </tbody>
            </table>
            
            @php
                $note_num = 1;
            @endphp

            @foreach ($steps as $step_note)
                @if (!empty($step_note->note))
                    <div class="notes">
                        <div style="text-align: justify;" class="notes-block">
                            <div class="note-title">Note {{$note_num++}}:</div>
                            <div class="notes-body">
                                {!! $step_note->note !!}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <table id="interfaces_table" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th style="border: 1px solid black; padding: 3px;">Documented Information Generated</th>
                        <th style="border: 1px solid black; padding: 3px;">References</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="padding-top:0">
                        <td style="vertical-align: top; padding-top: 0;">
                            <ol style="margin-top:0; margin-bottom: 0;">
                                @foreach ($uniqueOutputs  as $output)
                                <li>{{ $output->category }}: {{ $output->title }} </li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="vertical-align: top; padding-top: 0;">
                            <ol style="margin-top:0; margin-bottom: 0;">
                                @foreach ($uniqueInputs  as $input)
                                <li>{{ $input->category }}: {{ $input->title }} </li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                </tbody>
            </table>
        <div>
    </main>
</body>
</html>