/* Renderizador local e simples de gráficos: compatível com o uso básico de Chart.js neste projeto. */
(function () {
  const fallbackColors = ['#2878b8','#19a477','#e7a33e','#d9534f','#8063b6','#64748b','#db6d9e','#4b9ca8'];
  function Chart(target, config) { this.canvas = typeof target === 'string' ? document.querySelector(target) : target; this.config = config || {}; if (this.canvas) this.draw(); }
  Chart.prototype.draw = function () {
    const canvas = this.canvas; if (!canvas || !canvas.getContext) return;
    const parent = canvas.parentElement; const width = Math.max(260, parent ? parent.clientWidth : 560); const height = Math.max(180, parent ? parent.clientHeight : 280);
    const dpr = window.devicePixelRatio || 1; canvas.width = width*dpr; canvas.height = height*dpr; canvas.style.width = width+'px'; canvas.style.height = height+'px';
    const ctx = canvas.getContext('2d'); ctx.scale(dpr,dpr); ctx.clearRect(0,0,width,height);
    const data = this.config.data || {}; const labels = data.labels || []; const datasets = data.datasets || []; const type = this.config.type || 'bar';
    const values = datasets[0] && datasets[0].data ? datasets[0].data.map(v => Number(v)||0) : [];
    if (type === 'doughnut' || type === 'pie') {
      const total = values.reduce((a,b)=>a+b,0) || 1; const cx=width/2, cy=Math.min(height*0.43,height/2), radius=Math.min(width*0.28,height*0.34), inner=type==='doughnut'?radius*0.58:0;
      let start=-Math.PI/2; values.forEach((v,i)=>{const end=start+(v/total)*Math.PI*2;ctx.beginPath();ctx.arc(cx,cy,radius,start,end);if(inner)ctx.arc(cx,cy,inner,end,start,true);else ctx.lineTo(cx,cy);ctx.closePath();ctx.fillStyle=(datasets[0].backgroundColor||fallbackColors)[i%((datasets[0].backgroundColor||fallbackColors).length)]||fallbackColors[i%fallbackColors.length];ctx.fill();start=end;});
      ctx.fillStyle='#334155';ctx.font='bold 18px Arial';ctx.textAlign='center';ctx.fillText(String(values.reduce((a,b)=>a+b,0)),cx,cy+5);
      const legendY=Math.min(height-25,cy+radius+24);ctx.font='11px Arial';ctx.textAlign='left';let x=12,y=legendY;labels.forEach((label,i)=>{if(x>width-110){x=12;y+=16;}ctx.fillStyle=fallbackColors[i%fallbackColors.length];ctx.fillRect(x,y-8,8,8);ctx.fillStyle='#475569';ctx.fillText(String(label).slice(0,16),x+12,y);x+=Math.min(120,ctx.measureText(String(label)).width+28);});return;
    }
    const horizontal = this.config.options && this.config.options.indexAxis === 'y'; const left=horizontal?Math.min(120,width*0.38):38, right=14, top=12, bottom=horizontal?18:42; const plotW=width-left-right, plotH=height-top-bottom; const max=Math.max(1,...values)*1.15;
    ctx.strokeStyle='#cbd5e1';ctx.lineWidth=1;ctx.beginPath();ctx.moveTo(left,top);ctx.lineTo(left,top+plotH);ctx.lineTo(width-right,top+plotH);ctx.stroke();
    if(horizontal){const row=plotH/Math.max(values.length,1);values.forEach((v,i)=>{const y=top+i*row+row*.2;ctx.fillStyle='#475569';ctx.font='11px Arial';ctx.textAlign='right';ctx.fillText(String(labels[i]||'').slice(0,18),left-7,y+row*.38);ctx.fillStyle=fallbackColors[i%fallbackColors.length];ctx.fillRect(left,y,plotW*v/max,row*.6);ctx.fillStyle='#334155';ctx.textAlign='left';ctx.fillText(String(v),left+plotW*v/max+4,y+row*.38);});}
    else {const col=plotW/Math.max(values.length,1);values.forEach((v,i)=>{const barH=plotH*v/max,x=left+i*col+col*.2;ctx.fillStyle=fallbackColors[i%fallbackColors.length];ctx.fillRect(x,top+plotH-barH,col*.6,barH);ctx.fillStyle='#475569';ctx.font='10px Arial';ctx.textAlign='center';ctx.fillText(String(labels[i]||'').slice(0,10),x+col*.3,top+plotH+16);});}
  };
  Chart.prototype.destroy=function(){if(this.canvas){const c=this.canvas.getContext('2d');c&&c.clearRect(0,0,this.canvas.width,this.canvas.height);}};
  window.Chart=window.Chart||Chart;
})();
