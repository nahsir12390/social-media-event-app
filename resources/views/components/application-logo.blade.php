<svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Background Circle with Gradient -->
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
        </linearGradient>
        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="8" flood-opacity="0.2"/>
        </filter>
    </defs>

    <!-- Base Circle -->
    <circle cx="158" cy="158" r="140" fill="url(#logoGradient)" filter="url(#shadow)"/>
    
    <!-- Map Pin Outline -->
    <path d="M158 85 C132 85 110 107 110 133 C110 158 158 205 158 205 C158 205 206 158 206 133 C206 107 184 85 158 85 Z" 
          fill="white" 
          stroke="white" 
          stroke-width="4"/>
    
    <!-- Inner Circle (Map Pin Center) -->
    <circle cx="158" cy="133" r="23" fill="url(#logoGradient)"/>
    
    <!-- Location Dot -->
    <circle cx="158" cy="133" r="10" fill="white"/>
    
    <!-- Connection Lines (Network/Connectivity) -->
    <path d="M110 140 Q130 115 158 115 Q186 115 206 140" 
          stroke="white" 
          stroke-width="6" 
          fill="none" 
          stroke-linecap="round"/>
    
    <path d="M120 165 Q138 145 158 145 Q178 145 196 165" 
          stroke="white" 
          stroke-width="6" 
          fill="none" 
          stroke-linecap="round"/>
    
    <!-- Small Dots (People/Events) -->
    <circle cx="125" cy="125" r="4" fill="white"/>
    <circle cx="191" cy="125" r="4" fill="white"/>
    <circle cx="145" cy="170" r="4" fill="white"/>
    <circle cx="171" cy="170" r="4" fill="white"/>
    
    <!-- Wave Effect at Bottom (Community Vibe) -->
    <path d="M110 205 Q135 220 158 220 Q181 220 206 205" 
          stroke="white" 
          stroke-width="5" 
          fill="none" 
          stroke-opacity="0.8"/>
</svg>