{{--
  Branded, content-specific UI mockup — realistic, colourful product screens
  with depth (used for in-development products with no live site yet, and for
  service-category art). Pure inline SVG. The brand accent stays var(--color-accent)
  so it re-tints; the in-app UI uses its own vivid palette to match the colour
  and life of the real product screenshots elsewhere on the site.
--}}
@props(['for' => null, 'type' => null, 'label' => 'Interface preview'])

@php
    $map = [
        'recruitment263' => 'job-board', 'nestzim' => 'property', 'cv263' => 'resume',
        'shop263' => 'storefront', 'nicejob' => 'marketplace', 'wlsa-zimbabwe' => 'content',
        'Build' => 'build', 'Rank' => 'rank', 'Grow' => 'grow',
    ];
    $t = $type ?? ($map[$for] ?? 'app');
    $urls = [
        'job-board' => 'recruitment263.co.zw', 'property' => 'nestzim.co.zw', 'resume' => 'cv263.co.zw',
        'storefront' => 'shop263.co.zw', 'marketplace' => 'nicejob.co.zw', 'content' => 'wlsazim.co.zw',
        'build' => 'fignoc.co.zw', 'rank' => 'chatgpt · gemini · google', 'grow' => 'analytics',
        'app' => 'fignoc.co.zw',
    ];
    $url = $urls[$t] ?? 'fignoc.co.zw';
@endphp

<svg viewBox="0 0 400 250" role="img" aria-label="{{ $label }} preview"
     {{ $attributes->merge(['class' => 'mockup', 'style' => "width:100%;height:100%;display:block;font-family:'Satoshi',system-ui,sans-serif;"]) }}
     preserveAspectRatio="xMidYMid slice">
    <defs>
        <linearGradient id="mkbg" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#f7f8fc"/><stop offset="1" stop-color="#eef0f7"/>
        </linearGradient>
        <linearGradient id="mkhero" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#6366f1"/><stop offset="1" stop-color="#8b5cf6"/>
        </linearGradient>
        <linearGradient id="mkbar" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="var(--color-accent)"/><stop offset="1" stop-color="#8b5cf6"/>
        </linearGradient>
        <filter id="mkds" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#1e293b" flood-opacity="0.14"/>
        </filter>
    </defs>

    {{-- device + browser chrome --}}
    <rect width="400" height="250" fill="url(#mkbg)"/>
    <rect x="20" y="16" width="360" height="218" rx="13" fill="#ffffff" stroke="#e2e5ee"/>
    <rect x="20" y="16" width="360" height="30" rx="13" fill="#f4f5fa"/><rect x="20" y="38" width="360" height="8" fill="#f4f5fa"/>
    <circle cx="38" cy="31" r="3.5" fill="#fb7185"/><circle cx="50" cy="31" r="3.5" fill="#fbbf24"/><circle cx="62" cy="31" r="3.5" fill="#34d399"/>
    <rect x="86" y="24" width="226" height="14" rx="7" fill="#ffffff" stroke="#e2e5ee"/>
    <text x="96" y="34" font-size="8" fill="#94a3b8">{{ $url }}</text>
    <line x1="20" y1="46" x2="380" y2="46" stroke="#e2e5ee"/>

    @switch($t)
        @case('job-board')
            <rect x="34" y="54" width="20" height="20" rx="6" fill="#f97316"/><text x="44" y="68" text-anchor="middle" font-size="10" font-weight="800" fill="#fff">R</text>
            <text x="62" y="68" font-size="11" font-weight="700" fill="#0f172a">Recruitment263</text>
            <rect x="292" y="56" width="72" height="18" rx="9" fill="#f97316"/><text x="328" y="68" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">Post a job</text>
            <rect x="34" y="84" width="330" height="20" rx="10" fill="#f1f5f9" stroke="#e2e8f0"/><circle cx="48" cy="94" r="4.5" fill="none" stroke="#94a3b8" stroke-width="1.5"/><text x="62" y="97" font-size="8" fill="#94a3b8">Search 1,240 jobs across Zimbabwe</text>
            @foreach ([['Finance Officer','Harare · NGO','#2563eb','UN',1],['Programme Manager','Bulawayo · Donor','#16a34a','GO',0],['Software Developer','Harare · Tech','#7c3aed','FT',0]] as $i => $j)
                <rect x="34" y="{{ 112 + $i * 36 }}" width="330" height="30" rx="8" fill="#fff" stroke="#eef0f6" filter="url(#mkds)"/>
                <rect x="42" y="{{ 119 + $i * 36 }}" width="18" height="18" rx="6" fill="{{ $j[2] }}"/><text x="51" y="{{ 132 + $i * 36 }}" text-anchor="middle" font-size="7.5" font-weight="800" fill="#fff">{{ $j[3] }}</text>
                <text x="66" y="{{ 126 + $i * 36 }}" font-size="8.5" font-weight="700" fill="#0f172a">{{ $j[0] }}</text>
                <text x="66" y="{{ 137 + $i * 36 }}" font-size="7" fill="#94a3b8">{{ $j[1] }} · Full-time</text>
                <rect x="308" y="{{ 118 + $i * 36 }}" width="48" height="17" rx="8.5" fill="{{ $j[4] ? 'var(--color-accent)' : '#eef2ff' }}"/><text x="332" y="{{ 130 + $i * 36 }}" text-anchor="middle" font-size="7" font-weight="700" fill="{{ $j[4] ? '#fff' : '#4f46e5' }}">Apply</text>
            @endforeach
            @break

        @case('property')
            <rect x="34" y="54" width="188" height="162" rx="10" fill="#e8eef7"/>
            <path d="M34 120 q40 -18 90 4 t98 -6" fill="none" stroke="#c7d2e4" stroke-width="2"/>
            <path d="M34 160 q54 14 110 -6 t78 2" fill="none" stroke="#c7d2e4" stroke-width="2"/>
            <g filter="url(#mkds)"><rect x="60" y="86" width="42" height="18" rx="9" fill="#0f172a"/><text x="81" y="98" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">$450</text></g>
            <g filter="url(#mkds)"><rect x="140" y="150" width="42" height="18" rx="9" fill="var(--color-accent)"/><text x="161" y="162" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">$650</text></g>
            <circle cx="120" cy="122" r="6" fill="#ef4444" stroke="#fff" stroke-width="2"/>
            <g filter="url(#mkds)"><rect x="234" y="54" width="130" height="162" rx="10" fill="#fff"/></g>
            <rect x="234" y="54" width="130" height="70" rx="10" fill="#dbe4f0"/><rect x="234" y="110" width="130" height="14" fill="#dbe4f0"/>
            <path d="M270 74 h58 v34 h-58 z M270 108 l18 -18 14 12 12 -14 14 20 z" fill="#b9c7dd"/><circle cx="288" cy="84" r="6" fill="#cdd8e8"/>
            <rect x="244" y="62" width="52" height="16" rx="8" fill="#16a34a"/><text x="270" y="73" text-anchor="middle" font-size="7" font-weight="700" fill="#fff">✓ Verified</text>
            <text x="244" y="142" font-size="9" font-weight="700" fill="#0f172a">3 Bed House</text>
            <text x="244" y="154" font-size="7.5" fill="#94a3b8">Avondale, Harare</text>
            <text x="244" y="174" font-size="14" font-weight="800" fill="#0f172a">$650<tspan font-size="8" font-weight="500" fill="#94a3b8">/mo</tspan></text>
            <rect x="244" y="184" width="110" height="22" rx="7" fill="#0f172a"/><text x="299" y="198" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">Message agent</text>
            @break

        @case('resume')
            <rect x="30" y="54" width="150" height="162" rx="9" fill="#f8fafc" stroke="#eef0f6"/>
            <text x="44" y="74" font-size="7" font-weight="700" fill="#94a3b8">FULL NAME</text>
            <rect x="44" y="80" width="122" height="17" rx="5" fill="#fff" stroke="#dbe1ea"/><text x="52" y="92" font-size="8" fill="#0f172a">Tendai Moyo</text>
            <text x="44" y="112" font-size="7" font-weight="700" fill="#94a3b8">JOB TITLE</text>
            <rect x="44" y="118" width="122" height="17" rx="5" fill="#fff" stroke="#dbe1ea"/><text x="52" y="130" font-size="8" fill="#0f172a">Marketing Specialist</text>
            <text x="44" y="152" font-size="7" font-weight="700" fill="#94a3b8">TEMPLATE</text>
            <rect x="44" y="158" width="34" height="26" rx="4" fill="#eef2ff" stroke="#4f46e5"/><rect x="84" y="158" width="34" height="26" rx="4" fill="#fff" stroke="#dbe1ea"/><rect x="124" y="158" width="34" height="26" rx="4" fill="#fff" stroke="#dbe1ea"/>
            <g filter="url(#mkds)"><rect x="44" y="192" width="122" height="20" rx="6" fill="var(--color-accent)"/></g><text x="105" y="205" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">↓ Download PDF</text>
            <g filter="url(#mkds)"><rect x="196" y="54" width="168" height="162" rx="9" fill="#fff"/></g>
            <rect x="196" y="54" width="168" height="34" rx="9" fill="#0f172a"/><rect x="196" y="80" width="168" height="8" fill="#0f172a"/>
            <text x="210" y="72" font-size="10" font-weight="800" fill="#fff">Tendai Moyo</text><text x="210" y="82" font-size="7" fill="#cbd5e1">Marketing Specialist</text>
            <circle cx="344" cy="71" r="10" fill="var(--color-accent)"/><text x="344" y="74" text-anchor="middle" font-size="8" font-weight="800" fill="#fff">TM</text>
            <text x="210" y="106" font-size="7.5" font-weight="800" fill="var(--color-accent-deep)">EXPERIENCE</text>
            @foreach ([116, 127, 138, 156, 167] as $y)
                <rect x="210" y="{{ $y }}" width="{{ $y === 138 ? 80 : 140 }}" height="4.5" rx="2" fill="#e2e8f0"/>
            @endforeach
            <text x="210" y="152" font-size="7.5" font-weight="800" fill="var(--color-accent-deep)">SKILLS</text>
            <rect x="210" y="180" width="30" height="12" rx="6" fill="#eef2ff"/><rect x="244" y="180" width="34" height="12" rx="6" fill="#eef2ff"/><rect x="282" y="180" width="26" height="12" rx="6" fill="#eef2ff"/>
            @break

        @case('storefront')
            <rect x="20" y="46" width="360" height="26" fill="#0f172a"/><text x="34" y="63" font-size="11" font-weight="800" fill="#fff">Shop263</text>
            <text x="250" y="63" font-size="8" fill="#cbd5e1">Home · Shop · Cart</text>
            <circle cx="356" cy="59" r="9" fill="#1e293b"/><path d="M352 56 h8 l-1 6 h-6 z" fill="none" stroke="#fff" stroke-width="1.2"/><circle cx="362" cy="52" r="5" fill="var(--color-accent)"/><text x="362" y="55" text-anchor="middle" font-size="6.5" font-weight="800" fill="#fff">2</text>
            @foreach ([['Sneakers','$35','#fca5a5',0],['Denim Jacket','$48','#93c5fd',0],['Tote Bag','$18','#fcd34d',1],['Cap','$12','#86efac',0]] as $i => $p)
                @php $x = 34 + ($i % 2) * 168; $y = 82 + intdiv($i, 2) * 68; @endphp
                <g filter="url(#mkds)"><rect x="{{ $x }}" y="{{ $y }}" width="158" height="60" rx="8" fill="#fff"/></g>
                <rect x="{{ $x }}" y="{{ $y }}" width="158" height="34" rx="8" fill="{{ $p[2] }}"/><rect x="{{ $x }}" y="{{ $y + 26 }}" width="158" height="8" fill="{{ $p[2] }}"/>
                <text x="{{ $x + 10 }}" y="{{ $y + 48 }}" font-size="8" font-weight="600" fill="#0f172a">{{ $p[0] }}</text>
                <text x="{{ $x + 10 }}" y="{{ $y + 57 }}" font-size="8.5" font-weight="800" fill="#0f172a">{{ $p[1] }}</text>
                <rect x="{{ $x + 116 }}" y="{{ $y + 44 }}" width="34" height="15" rx="7.5" fill="{{ $p[3] ? 'var(--color-accent)' : '#0f172a' }}"/><text x="{{ $x + 133 }}" y="{{ $y + 54 }}" text-anchor="middle" font-size="7" font-weight="700" fill="#fff">Add</text>
            @endforeach
            @break

        @case('marketplace')
            <g filter="url(#mkds)"><circle cx="58" cy="82" r="22" fill="#8b5cf6"/></g><text x="58" y="88" text-anchor="middle" font-size="15" font-weight="800" fill="#fff">R</text>
            <circle cx="74" cy="98" r="6" fill="#22c55e" stroke="#fff" stroke-width="2"/>
            <text x="92" y="76" font-size="11" font-weight="800" fill="#0f172a">Rudo Chikodzi</text>
            <text x="92" y="89" font-size="8" fill="#94a3b8">Graphic Designer · Harare</text>
            <text x="92" y="106" font-size="9" fill="#f59e0b">★★★★★ <tspan font-size="8" font-weight="700" fill="#0f172a">4.9</tspan> <tspan fill="#94a3b8" font-weight="400">(32)</tspan></text>
            <g filter="url(#mkds)"><rect x="92" y="116" width="82" height="20" rx="6" fill="var(--color-accent)"/></g><text x="133" y="129" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">Hire me</text>
            <g filter="url(#mkds)"><rect x="34" y="150" width="210" height="58" rx="12" fill="#fff"/></g>
            <rect x="34" y="150" width="210" height="58" rx="12" fill="#f1f5f9"/>
            <text x="48" y="170" font-size="8" fill="#334155">Hi Rudo, are you available for a</text>
            <text x="48" y="182" font-size="8" fill="#334155">logo project this week? 🎨</text>
            <text x="48" y="198" font-size="7" fill="#94a3b8">delivered · 2m ago</text>
            <g filter="url(#mkds)"><rect x="286" y="164" width="64" height="34" rx="10" fill="#4f46e5"/></g><text x="318" y="184" text-anchor="middle" font-size="8" font-weight="700" fill="#fff">Reply</text>
            @break

        @case('content')
            <rect x="20" y="46" width="360" height="26" fill="#0e7490"/><text x="34" y="63" font-size="10" font-weight="800" fill="#fff">WLSA Zimbabwe</text>
            <text x="170" y="63" font-size="7.5" fill="#cffafe">Programmes · Research · News</text>
            <rect x="332" y="52" width="34" height="15" rx="7.5" fill="#fbbf24"/><text x="349" y="63" text-anchor="middle" font-size="7" font-weight="800" fill="#0f172a">Donate</text>
            <g filter="url(#mkds)"><rect x="34" y="82" width="214" height="60" rx="8" fill="#fff"/></g>
            <rect x="34" y="82" width="214" height="60" rx="8" fill="#e0f2fe"/><path d="M34 128 l40 -26 30 18 34 -22 40 30 z" fill="#bae6fd"/><circle cx="70" cy="102" r="8" fill="#7dd3fc"/>
            <text x="34" y="160" font-size="10" font-weight="800" fill="#0f172a">Legal Aid Programme</text>
            @foreach ([172, 183, 194] as $y)<rect x="34" y="{{ $y }}" width="{{ $y === 194 ? 120 : 214 }}" height="5" rx="2.5" fill="#e2e8f0"/>@endforeach
            <g filter="url(#mkds)"><rect x="262" y="82" width="102" height="126" rx="8" fill="#fff"/></g>
            <text x="274" y="100" font-size="7" font-weight="800" fill="#0e7490">RESOURCES</text>
            @foreach ([112, 128, 144, 160, 176] as $y)<rect x="274" y="{{ $y }}" width="78" height="5" rx="2.5" fill="#e2e8f0"/>@endforeach
            @break

        @case('build')
            <rect x="20" y="46" width="70" height="188" fill="#0f172a"/>
            <circle cx="34" cy="60" r="3" fill="#fb7185"/><circle cx="44" cy="60" r="3" fill="#fbbf24"/><circle cx="54" cy="60" r="3" fill="#34d399"/>
            <text x="30" y="82" font-size="7" fill="#64748b">EXPLORER</text>
            <text x="30" y="98" font-size="7" fill="#e2e8f0">▾ routes</text><text x="36" y="112" font-size="7" font-weight="700" fill="#fff">web.php</text>
            <text x="30" y="126" font-size="7" fill="#94a3b8">▾ app</text><text x="36" y="140" font-size="7" fill="#94a3b8">Product.php</text>
            <rect x="90" y="46" width="290" height="188" fill="#0b1120"/>
            <g font-family="ui-monospace,'Courier New',monospace" font-size="8">
                <text x="100" y="70" fill="#475569">1</text><text x="116" y="70" fill="#c084fc">Route</text><text x="146" y="70" fill="#e2e8f0">::get(</text><text x="182" y="70" fill="#86efac">'/'</text><text x="196" y="70" fill="#e2e8f0">, fn () =></text>
                <text x="100" y="86" fill="#475569">2</text><text x="132" y="86" fill="#e2e8f0">view(</text><text x="164" y="86" fill="#86efac">'home'</text><text x="200" y="86" fill="#e2e8f0">));</text>
                <text x="100" y="110" fill="#475569">4</text><text x="116" y="110" fill="#c084fc">class </text><text x="152" y="110" fill="#fbbf24">Product</text><text x="200" y="110" fill="#c084fc"> extends</text>
                <text x="100" y="126" fill="#475569">5</text><text x="132" y="126" fill="#fbbf24">Model</text><text x="168" y="126" fill="#e2e8f0"> {'{'}</text>
                <text x="100" y="142" fill="#475569">6</text><text x="132" y="142" fill="#60a5fa">protected</text><text x="188" y="142" fill="#e2e8f0"> $fillable;</text>
                <text x="100" y="158" fill="#475569">7</text><text x="132" y="158" fill="#e2e8f0">{'}'}</text>
            </g>
            <rect x="90" y="188" width="290" height="46" fill="#0f172a"/>
            <text x="104" y="206" font-family="ui-monospace,monospace" font-size="8" fill="#34d399">➜ npm run build</text>
            <text x="104" y="222" font-family="ui-monospace,monospace" font-size="8" fill="#86efac">✓ built in 4.1s · 0 errors</text>
            @break

        @case('rank')
            <rect x="34" y="56" width="330" height="22" rx="11" fill="#f1f5f9" stroke="#e2e8f0"/>
            <circle cx="50" cy="67" r="5" fill="none" stroke="#94a3b8" stroke-width="1.5"/><line x1="53" y1="70" x2="57" y2="74" stroke="#94a3b8" stroke-width="1.5"/>
            <text x="64" y="70" font-size="8.5" fill="#334155">who builds software in Harare?</text>
            <g filter="url(#mkds)"><rect x="34" y="90" width="330" height="96" rx="12" fill="#fff"/></g>
            <rect x="34" y="90" width="330" height="96" rx="12" fill="none" stroke="var(--color-accent)" stroke-width="1.5"/>
            <circle cx="52" cy="108" r="8" fill="url(#mkhero)"/><path d="M48 108 l3 3 5 -6" stroke="#fff" stroke-width="1.5" fill="none"/>
            <text x="66" y="111" font-size="8" font-weight="800" fill="var(--color-accent-deep)">AI Answer</text>
            <text x="48" y="130" font-size="8.5" fill="#0f172a"><tspan font-weight="800">Fignoc Technologies</tspan> is a Harare studio behind</text>
            <text x="48" y="143" font-size="8.5" fill="#0f172a">Recruitment263 and NestZim — and the country's</text>
            <text x="48" y="156" font-size="8.5" fill="#0f172a">only AEO/GEO specialist.</text>
            <rect x="48" y="166" width="96" height="15" rx="7.5" fill="#eef2ff"/><text x="60" y="176" font-size="7.5" font-weight="700" fill="#4f46e5">🔗 fignoc.co.zw</text>
            <text x="34" y="202" font-size="7.5" fill="#94a3b8">Cited across 3 trusted sources</text>
            @break

        @case('grow')
            <text x="34" y="70" font-size="7.5" font-weight="700" fill="#94a3b8">ENQUIRIES · THIS MONTH</text>
            <text x="34" y="94" font-size="22" font-weight="800" fill="#0f172a">128</text>
            <rect x="92" y="80" width="48" height="17" rx="8.5" fill="#dcfce7"/><text x="116" y="92" text-anchor="middle" font-size="8" font-weight="800" fill="#16a34a">↑ 38%</text>
            <g filter="url(#mkds)"><rect x="248" y="62" width="116" height="40" rx="8" fill="#fff"/></g>
            <text x="260" y="78" font-size="7" fill="#94a3b8">Conversion</text><text x="260" y="94" font-size="13" font-weight="800" fill="var(--color-accent-deep)">6.2%</text>
            <line x1="34" y1="200" x2="366" y2="200" stroke="#e2e8f0"/>
            @foreach ([['J',34,'#c7d2fe'],['F',48,'#c7d2fe'],['M',42,'#c7d2fe'],['A',66,'#a5b4fc'],['M',82,'#a5b4fc'],['J',108,'var(--color-accent)']] as $i => $b)
                <rect x="{{ 40 + $i * 55 }}" y="{{ 200 - $b[1] }}" width="36" height="{{ $b[1] }}" rx="4" fill="{{ $b[2] }}"/>
                <text x="{{ 58 + $i * 55 }}" y="212" text-anchor="middle" font-size="7" fill="#94a3b8">{{ $b[0] }}</text>
            @endforeach
            <polyline points="58,150 113,138 168,142 223,116 278,98 333,80" fill="none" stroke="#8b5cf6" stroke-width="2"/>
            @foreach ([[58,150],[113,138],[168,142],[223,116],[278,98],[333,80]] as $pt)<circle cx="{{ $pt[0] }}" cy="{{ $pt[1] }}" r="2.5" fill="#8b5cf6"/>@endforeach
            @break

        @default
            @foreach ([['Users','2,480','#eef2ff','#4f46e5'],['Active','1,120','#ecfdf5','#16a34a'],['Revenue','$12.4k','#fdf2f8','var(--color-accent-deep)']] as $i => $s)
                <g filter="url(#mkds)"><rect x="{{ 34 + $i * 112 }}" y="60" width="102" height="54" rx="8" fill="#fff"/></g>
                <rect x="{{ 34 + $i * 112 }}" y="60" width="102" height="54" rx="8" fill="{{ $s[2] }}"/>
                <text x="{{ 46 + $i * 112 }}" y="80" font-size="7" fill="#64748b">{{ $s[0] }}</text>
                <text x="{{ 46 + $i * 112 }}" y="100" font-size="14" font-weight="800" fill="{{ $s[3] }}">{{ $s[1] }}</text>
            @endforeach
            <g filter="url(#mkds)"><rect x="34" y="126" width="330" height="82" rx="8" fill="#fff"/></g>
            <polyline points="50,190 100,170 150,178 200,150 250,158 300,130 350,140" fill="none" stroke="var(--color-accent)" stroke-width="2"/>
    @endswitch
</svg>
