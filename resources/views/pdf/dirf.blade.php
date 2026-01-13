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
            font-size: 11pt;
            margin: 0;
            padding-top: 120px;   /* same as header height */
            padding-bottom: 20px; /* same as footer height */
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
            width: 100%;
            color: #000;
            font-size: 10pt;
        }
        #scope_objectives_table tbody tr td,
        #signatory_table tbody tr td,
        #interfaces_table tbody tr td, {
            border: 1px solid black;
            padding-left: 10px;
            padding-right: 5px;
        }
        #manual_name,
        #process_table thead tr{
            font-weight: 700;
            font-size: 12pt;
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
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        <table id="header_table">
            <tbody>
                <tr>
                    <td rowspan="2" style="font-size:18pt; text-align:center; width: 42%"><strong>FCU SOLUTIONS INC</strong></td>
                    <td style="border-bottom: 0; text-align: center; font-size: 18pt; font-weight: bold">DOCUMENTED INFORMATION<br> REQUEST FORM</td>
                </tr>
                <tr>
                    <td style="text-align:center; font-size: 8pt">F-DIM-001 <br> Rev. 0 10/01/2018</td>
                </tr>
            </tbody>
        </table>
        
    </header>

    <footer>
    </footer>

    <main>
        <div style="text-align: right; margin-bottom: 1rem;">
            <span>D.I.R.F No.: </span><span>______________</span>
        </div>

        <table id="scope_objectives_table">
            <tbody>
                <tr>
                    <td colspan="4">Type of Request:</td>
                    <td colspan="4">
                        (&nbsp;&nbsp;) New Documented Information
                    </td>
                    <td colspan="4">(&nbsp;&nbsp;) Documented Information Revision</td>
                    <td colspan="4">(&nbsp;&nbsp;) Documented Information Deletion</td>
                </tr>
                <tr>
                    <td colspan="4">Type of Documented Information:</td>
                    <td colspan="3">(&nbsp;&nbsp;) MS Manual Section </td>
                    <td colspan="3">(X) System Procedure</td>
                    <td colspan="3">(&nbsp;&nbsp;) Support Document</td>
                    <td colspan="3">(&nbsp;&nbsp;) Form</td>
                </tr>
                <tr>
                    <td colspan="16" style="height: 2.6rem; vertical-align: top;">Process:</td>
                </tr>
                <tr>
                    <td colspan="10" rowspan="2" style="vertical-align: top;">Documented Information Title/Proposed Title:</td>
                    <td colspan="6" style="font-style: italic; font-size: 10pt">To be filled out by Document Controller</td>
                </tr>
                <tr>
                    <td colspan="3" style="font-size: 10pt; height: 2.6rem; vertical-align: top;">Document No.:</td>
                    <td colspan="3" style="font-size: 10pt; height: 2.6rem; vertical-align: top;">Revision No.:</td>
                </tr>
                <tr>
                    <td colspan="16" style="height: 4rem; vertical-align: top;">Justification/Objective:</td>
                </tr>
                <tr>
                    <td colspan="16" style="height: 8rem; vertical-align: top;">Documented Information Details (which part was revised OR give an idea what the document looks like):</td>
                </tr>
                <tr>
                    <td colspan="16" style="height: 8rem; vertical-align: top;">
                        Affected documented information as a result of the new/revised/deleted documented information (these would have to be amended as well, no need for a new DIRF but the Documented Information Master List would have to be updated):
                        <table style="margin-bottom: 3rem">
                            <tr>
                                <td colspan="2" style="text-align: center; font-weight: bold; font-size: 8pt; width: 16%">
                                    Type of documented information<br>(MS Manual, System Procedure,<br>Support Document, or Form)
                                </td>
                                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 18%">Document No.</td>
                                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 19%">Revision No. (after this request has been approved):</td>
                                <td style="text-align: center; font-weight: bold; font-size: 8pt; width: 45%">Brief Details of Revision</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td style="width: 37%"></td>
                                <td style="width: 18%"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td style="width: 37%"></td>
                                <td style="width: 18%"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td style="width: 37%"></td>
                                <td style="width: 18%"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td style="width: 37%"></td>
                                <td style="width: 18%"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td style="width: 37%"></td>
                                <td style="width: 18%"></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" style="height: 2.3rem; vertical-align: top;">Prepared by:</td>
                    <td colspan="4"style="height: 2.3rem; vertical-align: top;">Date:</td>
                </tr>
                <tr>
                    <td colspan="12" style="height: 2.3rem; vertical-align: top;">Reviewed by:</td>
                    <td colspan="4"style="height: 2.3rem; vertical-align: top;">Date:</td>
                </tr>
                <tr>
                    <td colspan="12" style="height: 2.3rem; vertical-align: top;">Approved by:</td>
                    <td colspan="4"style="height: 2.3rem; vertical-align: top;">Date:</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>