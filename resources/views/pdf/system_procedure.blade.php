<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OnePage</title>

    <style>
        @page {
            margin: 75px 65px 30px 65px; /* top, right, bottom, left */
        }
        body {
            font-family: Helvetica, sans-serif;
            font-size: 13px;
            margin: 0;
            padding-top: 170px;   /* same as header height */
            padding-bottom: 30px; /* same as footer height */
        }
        header {
            position: fixed;
            top: 0; /* adjust depending on your @page top margin */
            left: 0;
            right: 0;
            height: 160px;

            /* Optional styling */
            text-align: center;
            /* border-bottom: 1px solid #000; */
            padding-bottom: 5px;
        }
        footer {
            position: fixed;
            bottom: -30px; /* adjust depending on your @page bottom margin */
            left: 0;
            right: 0;
            height: 30px;

            /* Optional styling */
            text-align: center;
            /* border: 1px solid black; */
            padding-top: 5px;
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
            font-size: 15px;
            background-color: lightblue;
            text-align: center;
        }
        #title-row{
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            text-transform: uppercase;
        }
        #scope_objectives_table tbody tr td:nth-child(2){
            width: 75%;
        }
        #scope_objectives_table ul{
            padding-left: 10px;
            margin: 0;
        }
        #process_table tbody{
            font-size: 12px;
            text-align: center;
        }
        .arrow-line {
            width: 0;
            height: 15px;
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
            margin-top: 0;
            margin-bottom: 0;
        }
        #process_table tbody tr:first-child td{
            padding-top: 10px
        }
        #process_table tbody tr:last-child td:nth-child(2){
            padding-bottom: 10px
        }
        p {
            orphans: 4;
            widows: 4;
        }
        #signatory_table {
            margin-top: 150px; /* push down */
            page-break-inside: avoid;
            width: 100%;
        }
        ol ol {
            list-style-type: lower-alpha; /* makes sublist a, b, c, etc. */
        }
        li {
            text-align: justify;
        }
        #scope_objectives_table p{
            padding: 0;
            margin: 0;
        }
        td ol{
            padding-left: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <table id="header_table">
            <tbody>
                <tr id="manual_name" style="width:60%">
                    <td colspan="3">SYSTEM PROCEDURES MANUAL</td>
                </tr>
                <tr>
                    <td rowspan="5" style="font-size:14px; text-align:center"><strong>NAME OF COMPANY</strong></td>
                    <td class="label_cell">Section No.:</td>
                    <td class="data_cell">{{ $doc->section_number }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Revision No.:</td>
                    <td class="data_cell">{{ $doc->revision_number }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Document No.:</td>
                    <td class="data_cell">{{ $doc->code }}</td>
                </tr>
                <tr>
                    <td class="label_cell">Effective Date:</td>
                    <td class="data_cell">{{ date("m/d/Y", strtotime($doc->effective_date)) }}</td>
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
        <span style="font-size: 9px; font-style: italic;"><strong>STRICTLY CONFIDENTIAL</strong> - For use of [NAME OF COMPANY] only. Unauthorized reproduction is strictly prohibited.</span>
    </header>

    <footer>
    </footer>

    <main>
        <table id="scope_objectives_table">
            <tbody>
                <tr>
                    <td><strong>OBJECTIVES</strong></td>
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

        <table id="process_table" style="margin-top: 20px; margin-bottom: 10px">
            <thead>
                <tr>
                    <th>Responsibility</th>
                    <th style="width: 35%;">Activities</th>
                    <th></th>
                    <th>Interfaces</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($steps as $key => $step)
                <tr>
                    <td>{{$step->responsibility}} </td>
                    <td style="width: 35%;">
                        @if ($key !== 0)
                        <div class="arrow-line" style="margin-top:0; margin-bottom: 0"></div>
                        <div class="arrow-down" style="margin-top:0; margin-bottom: 0"></div>
                        @endif
                        <p style="min-height: 50px; border: 1px solid black; padding: 10px; margin-bottom: 0; margin-top:0; vertical-align: middle">
                            {{$step->activities}}
                        </p>
                        @if ($key+1 !== $steps->count())
                        <div class="arrow-line" style="margin-top :0; margin-bottom: 0"></div>
                        @endif
                    </td>
                    <td>
                        @if ($step->note)
                            Note {{$note++}}
                        @endif
                    </td>
                    <td style="width: 30%;">
                        @foreach($step->interfaces as $interface)
                            {{$interface->category}}: {{$interface->title}} <br>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @php
            $note_num = 1;
        @endphp

        @foreach ($steps as $step_note)
            @if (!empty($step_note->note))
                <div style="margin-bottom: 0.5rem">
                    <p style="text-align: justify;">
                        <strong>Note {{$note_num++}}: </strong>
                        {!! $step_note->note !!}
                    </p>
                </div>
            @endif
        @endforeach

        <table id="interfaces_table">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 5px; height: 1.5rem">Documented Information Generated</th>
                    <th style="border: 1px solid black; padding: 5px; height: 1.5rem">References</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <ol>
                            @foreach ($uniqueOutputs  as $output)
                            <li>{{ $output->category }}: {{ $output->title }} </li>
                            @endforeach
                        </ol>
                    </td>
                    <td>
                        <ol>
                            @foreach ($uniqueInputs  as $input)
                            <li>{{ $input->category }}: {{ $input->title }} </li>
                            @endforeach
                        </ol>
                    </td>
                </tr>
            </tbody>
        </table>

        <table id="signatory_table">
            <tbody>
                <tr>
                    <td>Prepared By:</td>
                    <td>Reviewed By:</td>
                    <td>Approved By:</td>
                </tr>
                <tr>
                    <td style="height: 80px; vertical-align: bottom;">
                        Name <br>
                        Position
                    </td>
                    <td style="height: 80px; vertical-align: bottom;">
                        Name <br>
                        Position
                    </td>
                    <td style="height: 80px; vertical-align: bottom;">
                        Name <br>
                        Position
                    </td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>Date:</td>
                    <td>Date:</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>