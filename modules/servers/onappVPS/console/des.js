/*
 * Ported from Flashlight VNC ActionScript implementation:
 *     http://www.wizhelp.com/flashlight-vnc/
 *
 * Full attribution follows:
 *
 * -------------------------------------------------------------------------
 *
 * This DES class has been extracted from package Acme.Crypto for use in VNC.
 * The unnecessary odd parity code has been removed.
 *
 * These changes are:
 *  Copyright (C) 1999 AT&T Laboratories Cambridge.  All Rights Reserved.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *

 * DesCipher - the DES encryption method
 *
 * The meat of this code is by Dave Zimmerman <dzimm@widget.com>, and is:
 *
 * Copyright (c) 1996 Widget Workshop, Inc. All Rights Reserved.
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for NON-COMMERCIAL or COMMERCIAL purposes and
 * without fee is hereby granted, provided that this copyright notice is kept 
 * intact. 
 * 
 * WIDGET WORKSHOP MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE SUITABILITY
 * OF THE SOFTWARE, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. WIDGET WORKSHOP SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 * 
 * THIS SOFTWARE IS NOT DESIGNED OR INTENDED FOR USE OR RESALE AS ON-LINE
 * CONTROL EQUIPMENT IN HAZARDOUS ENVIRONMENTS REQUIRING FAIL-SAFE
 * PERFORMANCE, SUCH AS IN THE OPERATION OF NUCLEAR FACILITIES, AIRCRAFT
 * NAVIGATION OR COMMUNICATION SYSTEMS, AIR TRAFFIC CONTROL, DIRECT LIFE
 * SUPPORT MACHINES, OR WEAPONS SYSTEMS, IN WHICH THE FAILURE OF THE
 * SOFTWARE COULD LEAD DIRECTLY TO DEATH, PERSONAL INJURY, OR SEVERE
 * PHYSICAL OR ENVIRONMENTAL DAMAGE ("HIGH RISK ACTIVITIES").  WIDGET WORKSHOP
 * SPECIFICALLY DISCLAIMS ANY EXPRESS OR IMPLIED WARRANTY OF FITNESS FOR
 * HIGH RISK ACTIVITIES.
 *
 *
 * The rest is:
 *
 * Copyright (C) 1996 by Jef Poskanzer <jef@acme.com>.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 *
 * Visit the ACME Labs Java page for up-to-date versions of this and other
 * fine Java utilities: http://www.acme.com/java/
 */
"use strict";function DES(e){function b(e){var r,i,s,o,u,a,f=[],l=[],c=[],h,p,d,v;for(i=0,s=56;i<56;++i,s-=8)s+=s<-5?65:s<-3?31:s<-1?63:s===27?35:0,o=s&7,f[i]=(e[s>>>3]&1<<o)!==0?1:0;for(r=0;r<16;++r){o=r<<1,u=o+1,c[o]=c[u]=0;for(a=28;a<59;a+=28)for(i=a-28;i<a;++i)s=i+n[r],s<a?l[i]=f[s]:l[i]=f[s-28];for(i=0;i<24;++i)l[t[i]]!==0&&(c[o]|=1<<23-i),l[t[i+24]]!==0&&(c[u]|=1<<23-i)}for(r=0,d=0,v=0;r<16;++r)h=c[d++],p=c[d++],y[v]=(h&16515072)<<6,y[v]|=(h&4032)<<10,y[v]|=(p&16515072)>>>10,y[v]|=(p&4032)>>>6,++v,y[v]=(h&258048)<<12,y[v]|=(h&63)<<16,y[v]|=(p&258048)>>>4,y[v]|=p&63,++v}function w(e){var t=0,n=e.slice(),r,i=0,s,o,u;s=n[t++]<<24|n[t++]<<16|n[t++]<<8|n[t++],o=n[t++]<<24|n[t++]<<16|n[t++]<<8|n[t++],u=(s>>>4^o)&252645135,o^=u,s^=u<<4,u=(s>>>16^o)&65535,o^=u,s^=u<<16,u=(o>>>2^s)&858993459,s^=u,o^=u<<2,u=(o>>>8^s)&16711935,s^=u,o^=u<<8,o=o<<1|o>>>31&1,u=(s^o)&2863311530,s^=u,o^=u,s=s<<1|s>>>31&1;for(t=0;t<8;++t)u=o<<28|o>>>4,u^=y[i++],r=m[u&63],r|=d[u>>>8&63],r|=h[u>>>16&63],r|=l[u>>>24&63],u=o^y[i++],r|=g[u&63],r|=v[u>>>8&63],r|=p[u>>>16&63],r|=c[u>>>24&63],s^=r,u=s<<28|s>>>4,u^=y[i++],r=m[u&63],r|=d[u>>>8&63],r|=h[u>>>16&63],r|=l[u>>>24&63],u=s^y[i++],r|=g[u&63],r|=v[u>>>8&63],r|=p[u>>>16&63],r|=c[u>>>24&63],o^=r;o=o<<31|o>>>1,u=(s^o)&2863311530,s^=u,o^=u,s=s<<31|s>>>1,u=(s>>>8^o)&16711935,o^=u,s^=u<<8,u=(s>>>2^o)&858993459,o^=u,s^=u<<2,u=(o>>>16^s)&65535,s^=u,o^=u<<16,u=(o>>>4^s)&252645135,s^=u,o^=u<<4,u=[o,s];for(t=0;t<8;t++)n[t]=(u[t>>>2]>>>8*(3-t%4))%256,n[t]<0&&(n[t]+=256);return n}function E(e){return w(e.slice(0,8)).concat(w(e.slice(8,16)))}var t=[13,16,10,23,0,4,2,27,14,5,20,9,22,18,11,3,25,7,15,6,26,19,12,1,40,51,30,36,46,54,29,39,50,44,32,47,43,48,38,55,33,52,45,41,49,35,28,31],n=[1,2,4,6,8,10,12,14,15,17,19,21,23,25,27,28],r=0,i,s,o,u,a,f,l,c,h,p,d,v,m,g,y=[];return i=65536,s=1<<24,o=i|s,u=4,a=1024,f=u|a,l=[o|a,r|r,i|r,o|f,o|u,i|f,r|u,i|r,r|a,o|a,o|f,r|a,s|f,o|u,s|r,r|u,r|f,s|a,s|a,i|a,i|a,o|r,o|r,s|f,i|u,s|u,s|u,i|u,r|r,r|f,i|f,s|r,i|r,o|f,r|u,o|r,o|a,s|r,s|r,r|a,o|u,i|r,i|a,s|u,r|a,r|u,s|f,i|f,o|f,i|u,o|r,s|f,s|u,r|f,i|f,o|a,r|f,s|a,s|a,r|r,i|u,i|a,r|r,o|u],i=1<<20,s=1<<31,o=i|s,u=32,a=32768,f=u|a,c=[o|f,s|a,r|a,i|f,i|r,r|u,o|u,s|f,s|u,o|f,o|a,s|r,s|a,i|r,r|u,o|u,i|a,i|u,s|f,r|r,s|r,r|a,i|f,o|r,i|u,s|u,r|r,i|a,r|f,o|a,o|r,r|f,r|r,i|f,o|u,i|r,s|f,o|r,o|a,r|a,o|r,s|a,r|u,o|f,i|f,r|u,r|a,s|r,r|f,o|a,i|r,s|u,i|u,s|f,s|u,i|u,i|a,r|r,s|a,r|f,s|r,o|u,o|f,i|a],i=1<<17,s=1<<27,o=i|s,u=8,a=512,f=u|a,h=[r|f,o|a,r|r,o|u,s|a,r|r,i|f,s|a,i|u,s|u,s|u,i|r,o|f,i|u,o|r,r|f,s|r,r|u,o|a,r|a,i|a,o|r,o|u,i|f,s|f,i|a,i|r,s|f,r|u,o|f,r|a,s|r,o|a,s|r,i|u,r|f,i|r,o|a,s|a,r|r,r|a,i|u,o|f,s|a,s|u,r|a,r|r,o|u,s|f,i|r,s|r,o|f,r|u,i|f,i|a,s|u,o|r,s|f,r|f,o|r,i|f,r|u,o|u,i|a],i=8192,s=1<<23,o=i|s,u=1,a=128,f=u|a,p=[o|u,i|f,i|f,r|a,o|a,s|f,s|u,i|u,r|r,o|r,o|r,o|f,r|f,r|r,s|a,s|u,r|u,i|r,s|r,o|u,r|a,s|r,i|u,i|a,s|f,r|u,i|a,s|a,i|r,o|a,o|f,r|f,s|a,s|u,o|r,o|f,r|f,r|r,r|r,o|r,i|a,s|a,s|f,r|u,o|u,i|f,i|f,r|a,o|f,r|f,r|u,i|r,s|u,i|u,o|a,s|f,i|u,i|a,s|r,o|u,r|a,s|r,i|r,o|a],i=1<<25,s=1<<30,o=i|s,u=256,a=1<<19,f=u|a,d=[r|u,i|f,i|a,o|u,r|a,r|u,s|r,i|a,s|f,r|a,i|u,s|f,o|u,o|a,r|f,s|r,i|r,s|a,s|a,r|r,s|u,o|f,o|f,i|u,o|a,s|u,r|r,o|r,i|f,i|r,o|r,r|f,r|a,o|u,r|u,i|r,s|r,i|a,o|u,s|f,i|u,s|r,o|a,i|f,s|f,r|u,i|r,o|a,o|f,r|f,o|r,o|f,i|a,r|r,s|a,o|r,r|f,i|u,s|u,r|a,r|r,s|a,i|f,s|u],i=1<<22,s=1<<29,o=i|s,u=16,a=16384,f=u|a,v=[s|u,o|r,r|a,o|f,o|r,r|u,o|f,i|r,s|a,i|f,i|r,s|u,i|u,s|a,s|r,r|f,r|r,i|u,s|f,r|a,i|a,s|f,r|u,o|u,o|u,r|r,i|f,o|a,r|f,i|a,o|a,s|r,s|a,r|u,o|u,i|a,o|f,i|r,r|f,s|u,i|r,s|a,s|r,r|f,s|u,o|f,i|a,o|r,i|f,o|a,r|r,o|u,r|u,r|a,o|r,i|f,r|a,i|u,s|f,r|r,o|a,s|r,i|u,s|f],i=1<<21,s=1<<26,o=i|s,u=2,a=2048,f=u|a,m=[i|r,o|u,s|f,r|r,r|a,s|f,i|f,o|a,o|f,i|r,r|r,s|u,r|u,s|r,o|u,r|f,s|a,i|f,i|u,s|a,s|u,o|r,o|a,i|u,o|r,r|a,r|f,o|f,i|a,r|u,s|r,i|a,s|r,i|a,i|r,s|f,s|f,o|u,o|u,r|u,i|u,s|r,s|a,i|r,o|a,r|f,i|f,o|a,r|f,s|u,o|f,o|r,i|a,r|r,r|u,o|f,r|r,i|f,o|r,r|a,s|u,s|a,r|a,i|u],i=1<<18,s=1<<28,o=i|s,u=64,a=4096,f=u|a,g=[s|f,r|a,i|r,o|f,s|r,s|f,r|u,s|r,i|u,o|r,o|f,i|a,o|a,i|f,r|a,r|u,o|r,s|u,s|a,r|f,i|a,i|u,o|u,o|a,r|f,r|r,r|r,o|u,s|u,s|a,i|f,i|r,i|f,i|r,o|a,r|a,r|u,o|u,r|a,i|f,s|a,r|u,s|u,o|r,o|u,s|r,i|r,s|f,r|r,o|f,i|u,s|u,o|r,s|a,s|f,r|r,o|f,i|a,i|a,r|f,r|f,i|u,s|r,o|a],b(e),{encrypt:E}};