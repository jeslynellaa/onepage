<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OnePage</title>

    <style>
        @page {
            margin: 75px 65px 50px 65px; /* top, right, bottom, left */
        }
        body {
            font-family: Helvetica, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding-top: 145px;   /* same as header height */
            padding-bottom: 20px; /* same as footer height */
            /* border: 3px solid red; */
        }
        header {
            position: fixed;
            top: -25; /* adjust depending on your @page top margin */
            left: 0;
            right: 0;
            height: 170px;
            /* border: 3px solid yellow; */

            /* Optional styling */
            text-align: center;
            /* border-bottom: 1px solid #000; */
        }
        footer {
            position: fixed;
            bottom: -50px; /* adjust depending on your @page bottom margin */
            left: 0;
            right: 0;
            height: 50px;
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
            background-color: lightblue;
            text-align: center;
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
        p {
            orphans: 4;
            widows: 4;
        }
        #signatory_table {
            font-size: 10pt;
            page-break-inside: avoid;
            width: 100%;
            margin-bottom: 15px;
        }
        #signatory_table tr:nth-child(2) td{
            text-align: center;
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
        .connector{
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
        ul li{
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .signatory {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        @page :last {
            margin-bottom: 100px;
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
                    <td rowspan="5" style="font-size:14pt; text-align:center"><strong>FCU SOLUTIONS INC</strong></td>
                    <td class="label_cell">Section No.:</td>
                    <td class="data_cell">{{ $doc->section_number }}</td>
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

    <footer>
        <span style="font-style:italic; font-weight: bold;">Except for the MASTER COPY, printed and downloaded copies of this documented information are considered uncontrolled.</span>
    </footer>

    <main>
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

            <table id="process_table" style="margin-top: 15px;">
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
                                <div class="connector" style="background-image: url('data:image/png;base64,{{ base64_encode(file_get_contents($connector)) }}');">
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
                                <div class="connector" style="margin-top: 8px; background-image: url('data:image/png;base64,{{ base64_encode(file_get_contents($connector)) }}');">
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
                        <td style="height: 70px; width:200px; border: 1px solid black;">
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
                    <div style="margin-bottom: 0.5rem; page-break-inside: avoid;">
                        <p style="text-align: justify;">
                            <strong>Note {{$note_num++}}: </strong>
                            {!! $step_note->note !!}
                        </p>
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
                            <ol>
                                @foreach ($uniqueOutputs  as $output)
                                <li>{{ $output->category }}: {{ $output->title }} </li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="vertical-align: top; padding-top: 0;">
                            <ol>
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
    <div class="signatory">
        <table id="signatory_table">
            <tbody>
                <tr>
                    <td>Prepared By:</td>
                    <td>Reviewed By:</td>
                    <td>Approved By:</td>
                </tr>
                <tr>
                    <td style="height: 60px; vertical-align: bottom;">
                        Name <br>
                        Position
                    </td>
                    <td style="height: 60px; vertical-align: bottom;">
                        Staff Reviewer<br>
                        Reviewer
                    </td>
                    <td style="height: 60px; vertical-align: bottom;">
                        Staff Approver<br>
                        Approver
                    </td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>Date:</td>
                    <td>Date:</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>