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
            font-family: {{$font}};
            font-size: 11pt;
            margin: 0;
            padding-top: 150px;   /* same as header height */
            padding-bottom: 120px; /* same as footer height + signatory table height */
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
        #scope_objectives_table p{
            padding: 0;
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
        .note-title {
            font-weight: 700;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }
        /* ==========================================================================
        Quill JS v2.0+ Data-Attribute List Engine for Dompdf
        ========================================================================== */

        /* 1. Global Reset for Container Tags */
        .notes ol, 
        .notes ul {
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 0 !important; 
            list-style-type: none !important;
        }

        /* 2. Base Structural Layout for ALL List Items */
        .notes li[data-list] {
            list-style: none !important;
            list-style-type: none !important;
            position: relative !important;
            margin-left: 30px !important;    /* Base indent lane for Tier 0 numbers */
            padding-left: 30px !important;   /* Spacing between the text and marker block */
            text-align: justify;
        }

        /* 3. Global Marker Boundary Box Setup */
        .notes li[data-list]::before {
            position: absolute !important;
            left: 0 !important;
            width: 24px !important; 
            text-align: right !important;
        }

        /* ==========================================================================
        Counter Initialization Engine
        ========================================================================== */
        .notes {
            counter-reset: ql-0 ql-1 ql-2 ql-3 ql-4 ql-5;
        }
        /* ==========================================================================
   HTML Container Reset 
   Forces fresh <ol> wrappers to reset the custom Quill engine counters back to 0
   ========================================================================== */
.notes ol {
    counter-reset: ql-0 ql-1 ql-2 ql-3 ql-4 ql-5;
}

        /* ==========================================================================
        Universal Tier System (Relying on Cascading Specificity)
        ========================================================================== */

        /* --- Tier 0 (Base Level: e.g., 1., 2. or top-level bullet) --- */
        /* Simple selectors here act as the default fallback for all lists */
        .notes li[data-list="ordered"] { 
            counter-increment: ql-0; 
            counter-reset: ql-1 ql-2 ql-3 ql-4 ql-5; 
        }
        .notes li[data-list="ordered"]::before { content: counter(ql-0, decimal) '. '; }
        .notes li[data-list="bullet"]::before { content: "• " !important; font-size: 11pt; }


        /* --- Tier 1 (.ql-indent-1 / e.g., a., b.) --- */
        /* The addition of the class increases specificity, overriding Tier 0 rules */
        .notes .ql-indent-1 { margin-left: 60px !important; }
        .notes li[data-list="ordered"].ql-indent-1 { 
            counter-increment: ql-1; 
            counter-reset: ql-2 ql-3 ql-4 ql-5; 
        }
        .notes li[data-list="ordered"].ql-indent-1::before { content: counter(ql-1, lower-alpha) '. '; }
        .notes li[data-list="bullet"].ql-indent-1::before { content: "• " !important; font-size: 11pt; }


        /* --- Tier 2 (.ql-indent-2 / e.g., i., ii.) --- */
        .notes .ql-indent-2 { margin-left: 90px !important; }
        .notes li[data-list="ordered"].ql-indent-2 { 
            counter-increment: ql-2; 
            counter-reset: ql-3 ql-4 ql-5; 
        }
        .notes li[data-list="ordered"].ql-indent-2::before { content: counter(ql-2, lower-roman) '. '; }
        .notes li[data-list="bullet"].ql-indent-2::before { content: "• " !important; font-size: 11pt; }


        /* --- Tier 3 (.ql-indent-3 / Bullet Points) --- */
        .notes .ql-indent-3 { margin-left: 120px !important; }
        .notes li[data-list="ordered"].ql-indent-3 { 
            counter-increment: ql-3; 
            counter-reset: ql-4 ql-5;
        }
        .notes li[data-list="ordered"].ql-indent-3::before { content: counter(ql-3, decimal) '. '; }
        .notes li[data-list="bullet"].ql-indent-3::before { content: "• " !important; font-size: 11pt; }


        /* --- Tier 4 (.ql-indent-4) --- */
        .notes .ql-indent-4 { margin-left: 150px !important; }
        .notes li[data-list="ordered"].ql-indent-4 { 
            counter-increment: ql-4; 
            counter-reset: ql-5;
        }
        .notes li[data-list="ordered"].ql-indent-4::before { content: counter(ql-4, lower-alpha) '. '; }
        .notes li[data-list="bullet"].ql-indent-4::before { content: "• " !important; font-size: 11pt; }


        /* --- Tier 5 (.ql-indent-5) --- */
        .notes .ql-indent-5 { margin-left: 180px !important; }
        .notes li[data-list="ordered"].ql-indent-5 { counter-increment: ql-5; }
        .notes li[data-list="ordered"].ql-indent-5::before { content: counter(ql-5, lower-roman) '. '; }
        .notes li[data-list="bullet"].ql-indent-5::before { content: "• " !important; font-size: 11pt; }

        /* Table Isolation Linkage */
        td ol, td ul { padding-left: 1rem; }
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
        <span style="font-size: 9pt; font-style: italic;"><strong>STRICTLY CONFIDENTIAL</strong> - For use of <span style="text-transform: uppercase;">{{ $doc->company->name }}</span> only. Unauthorized reproduction is strictly prohibited.</span>
    </header>

    <footer>
        <span style="font-style:italic; font-weight: bold;">Except for the MASTER COPY AND CONTROLLED COPIES, printed and downloaded copies of this documented information are considered uncontrolled.</span>
    </footer>

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

            @if($definitionOfTerms->isNotEmpty())
                <div style="font-weight: 700; margin: 10px 0 4px;">DEFINITION OF TERMS</div>
                <table id="definition_of_terms_table" style="page-break-inside: avoid; margin-bottom: 8px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid black; padding: 3px; width: 30%;">Term</th>
                            <th style="border: 1px solid black; padding: 3px;">Definition</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($definitionOfTerms as $item)
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; vertical-align: top;"><strong>{{ $item->term }}</strong></td>
                                <td style="border: 1px solid black; padding: 5px; text-align: justify;">{{ $item->definition }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

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
                    
                    @php
                        $connectors = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                        $counter = 0;
                    @endphp

                    @foreach ($steps as $key => $step)
                    @if ($key !== 0 && $key % 5 === 0)
                        {{-- connector before breaking --}}
                        <tr style="border-bottom: 1px solid black;">
                            <td></td>
                            <td style="text-align:center;">
                                <div class="connector" style="background-image: url('data:image/png;base64,{{ base64_encode(file_get_contents($connector)) }}');">
                                    {{ $connectors[$counter] }}
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
                                    {{ $connectors[$counter] }}
                                    @php
                                        $counter++;
                                    @endphp
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
                        <div style="text-align: justify;">
                            <div class="note-title">Note {{$note_num++}}:</div>
                            {!! $step_note->note !!}
                        </div>
                    </div>
                @endif
            @endforeach

            <table id="interfaces_table" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th style="border: 1px solid black; padding: 3px; width: 50%;">Documented Information Generated</th>
                        <th style="border: 1px solid black; padding: 3px;">References</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="padding-top:0">
                        <td style="vertical-align: top; padding-top: 0; width: 50%;">
                            <ol style="margin-top:0; margin-bottom: 0;">
                                @forelse ($uniqueOutputs  as $output)
                                <li>{{ $output->category }}: {{ $output->title }} </li>
                                @empty
                                <div style="text-align: center">- No Document Outputs -</div>
                                @endforelse
                            </ol>
                        </td>
                        <td style="vertical-align: top; padding-top: 0;">
                            <ol style="margin-top:0; margin-bottom: 0;">
                                @forelse ($uniqueInputs  as $input)
                                <li>{{ $input->category }}: {{ $input->title }} </li>
                                @empty
                                <div style="text-align: center">- No References -</div>
                                @endforelse
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
                    <td style="height: 70px; vertical-align: bottom;">
                        @if($submitted)
                            <img src="{{ $owner_sign }}"
                                style="height: 35px;" >
                            <span style="font-style: italic;">(e-signed)</span><br>
                        @endif
                        <strong>{{ $doc->section->processOwner->fullname() }}</strong> <br>
                        Process Owner
                    </td>
                    <td style="height: 70px; vertical-align: bottom;">
                        @if($passed)
                            <img src="{{ $reviewer_sign }}"
                                style="height: 35px;" >
                            <span style="font-style: italic;">(e-signed)</span><br>
                        @else
                        
                        @endif
                        <strong>{{ $doc->section->reviewer->fullname() }}</strong><br>
                        {{ $doc->section->reviewer->position ?? 'Reviewer' }}
                    </td>
                    <td style="height: 70px; vertical-align: bottom;">
                        @if($approved)
                            <img src="{{ $approver_sign }}"
                                style="height: 35px;" >
                            <span style="font-style: italic;">(e-signed)</span><br>
                        @else
                        
                        @endif
                        <strong>{{ $doc->section->approver->fullname() }}</strong><br>
                        {{ $doc->section->approver->position ?? 'Approver' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Date:
                        @if($submitted)
                            {{ $submitted->performed_at->format('F j, Y') }}
                        @elseif($doc->status == 'Active')
                            {{  date("m/d/Y", strtotime($doc->effective_date)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        Date:
                        @if($passed)
                            {{ $passed->performed_at->format('F j, Y') }}
                        @elseif($doc->status == 'Active')
                            {{  date("m/d/Y", strtotime($doc->effective_date)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        Date:
                        @if($approved)
                            {{ $approved->performed_at->format('F j, Y') }}
                        @elseif($doc->status == 'Active')
                            {{  date("m/d/Y", strtotime($doc->effective_date)) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>