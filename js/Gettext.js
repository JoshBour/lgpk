Gettext=function(e){this.domain="messages";this.locale_data=undefined;var t=["domain","locale_data"];if(this.isValidObject(e)){for(var n in e){for(var r=0;r<t.length;r++){if(n==t[r]){if(this.isValidObject(e[n]))this[n]=e[n]}}}}this.try_load_lang();return this};Gettext.context_glue="";Gettext._locale_data={};Gettext.prototype.try_load_lang=function(){if(typeof this.locale_data!="undefined"){var e=this.locale_data;this.locale_data=undefined;this.parse_locale_data(e);if(typeof Gettext._locale_data[this.domain]=="undefined"){throw new Error("Error: Gettext 'locale_data' does not contain the domain '"+this.domain+"'")}}var t=this.get_lang_refs();if(typeof t=="object"&&t.length>0){for(var n=0;n<t.length;n++){var r=t[n];if(r.type=="application/json"){if(!this.try_load_lang_json(r.href)){throw new Error("Error: Gettext 'try_load_lang_json' failed. Unable to exec xmlhttprequest for link ["+r.href+"]")}}else if(r.type=="application/x-po"){if(!this.try_load_lang_po(r.href)){throw new Error("Error: Gettext 'try_load_lang_po' failed. Unable to exec xmlhttprequest for link ["+r.href+"]")}}else{throw new Error("TODO: link type ["+r.type+"] found, and support is planned, but not implemented at this time.")}}}};Gettext.prototype.parse_locale_data=function(e){if(typeof Gettext._locale_data=="undefined"){Gettext._locale_data={}}for(var t in e){if(!e.hasOwnProperty(t)||!this.isValidObject(e[t]))continue;var n=false;for(var r in e[t]){n=true;break}if(!n)continue;var i=e[t];if(t=="")t="messages";if(!this.isValidObject(Gettext._locale_data[t]))Gettext._locale_data[t]={};if(!this.isValidObject(Gettext._locale_data[t].head))Gettext._locale_data[t].head={};if(!this.isValidObject(Gettext._locale_data[t].msgs))Gettext._locale_data[t].msgs={};for(var s in i){if(s==""){var o=i[s];for(var u in o){var a=u.toLowerCase();Gettext._locale_data[t].head[a]=o[u]}}else{Gettext._locale_data[t].msgs[s]=i[s]}}}for(var t in Gettext._locale_data){if(this.isValidObject(Gettext._locale_data[t].head["plural-forms"])&&typeof Gettext._locale_data[t].head.plural_func=="undefined"){var f=Gettext._locale_data[t].head["plural-forms"];var l=new RegExp("^(\\s*nplurals\\s*=\\s*[0-9]+\\s*;\\s*plural\\s*=\\s*(?:\\s|[-\\?\\|&=!<>+*/%:;a-zA-Z0-9_()])+)","m");if(l.test(f)){var c=Gettext._locale_data[t].head["plural-forms"];if(!/;\s*$/.test(c))c=c.concat(";");var h="var plural; var nplurals; "+c+' return { "nplural" : nplurals, "plural" : (plural === true ? 1 : plural ? plural : 0) };';Gettext._locale_data[t].head.plural_func=new Function("n",h)}else{throw new Error("Syntax error in language file. Plural-Forms header is invalid ["+f+"]")}}else if(typeof Gettext._locale_data[t].head.plural_func=="undefined"){Gettext._locale_data[t].head.plural_func=function(e){var t=e!=1?1:0;return{nplural:2,plural:t}}}}return};Gettext.prototype.try_load_lang_po=function(e){var t=this.sjax(e);if(!t)return;var n=this.uri_basename(e);var r=this.parse_po(t);var i={};if(r){if(!r[""])r[""]={};if(!r[""]["domain"])r[""]["domain"]=n;n=r[""]["domain"];i[n]=r;this.parse_locale_data(i)}return 1};Gettext.prototype.uri_basename=function(e){var t;if(t=e.match(/^(.*\/)?(.*)/)){var n;if(n=t[2].match(/^(.*)\..+$/))return n[1];else return t[2]}else{return""}};Gettext.prototype.parse_po=function(e){var t={};var n={};var r="";var i=[];var s=e.split("\n");for(var o=0;o<s.length;o++){s[o]=s[o].replace(/(\n|\r)+$/,"");var u;if(/^$/.test(s[o])){if(typeof n["msgid"]!="undefined"){var a=typeof n["msgctxt"]!="undefined"&&n["msgctxt"].length?n["msgctxt"]+Gettext.context_glue+n["msgid"]:n["msgid"];var f=typeof n["msgid_plural"]!="undefined"&&n["msgid_plural"].length?n["msgid_plural"]:null;var l=[];for(var c in n){var u;if(u=c.match(/^msgstr_(\d+)/))l[parseInt(u[1])]=n[c]}l.unshift(f);if(l.length>1)t[a]=l;n={};r=""}}else if(/^#/.test(s[o])){continue}else if(u=s[o].match(/^msgctxt\s+(.*)/)){r="msgctxt";n[r]=this.parse_po_dequote(u[1])}else if(u=s[o].match(/^msgid\s+(.*)/)){r="msgid";n[r]=this.parse_po_dequote(u[1])}else if(u=s[o].match(/^msgid_plural\s+(.*)/)){r="msgid_plural";n[r]=this.parse_po_dequote(u[1])}else if(u=s[o].match(/^msgstr\s+(.*)/)){r="msgstr_0";n[r]=this.parse_po_dequote(u[1])}else if(u=s[o].match(/^msgstr\[0\]\s+(.*)/)){r="msgstr_0";n[r]=this.parse_po_dequote(u[1])}else if(u=s[o].match(/^msgstr\[(\d+)\]\s+(.*)/)){r="msgstr_"+u[1];n[r]=this.parse_po_dequote(u[2])}else if(/^"/.test(s[o])){n[r]+=this.parse_po_dequote(s[o])}else{i.push("Strange line ["+o+"] : "+s[o])}}if(typeof n["msgid"]!="undefined"){var a=typeof n["msgctxt"]!="undefined"&&n["msgctxt"].length?n["msgctxt"]+Gettext.context_glue+n["msgid"]:n["msgid"];var f=typeof n["msgid_plural"]!="undefined"&&n["msgid_plural"].length?n["msgid_plural"]:null;var l=[];for(var c in n){var u;if(u=c.match(/^msgstr_(\d+)/))l[parseInt(u[1])]=n[c]}l.unshift(f);if(l.length>1)t[a]=l;n={};r=""}if(t[""]&&t[""][1]){var h={};var p=t[""][1].split(/\\n/);for(var o=0;o<p.length;o++){if(!p.length)continue;var d=p[o].indexOf(":",0);if(d!=-1){var v=p[o].substring(0,d);var m=p[o].substring(d+1);var g=v.toLowerCase();if(h[g]&&h[g].length){i.push("SKIPPING DUPLICATE HEADER LINE: "+p[o])}else if(/#-#-#-#-#/.test(g)){i.push("SKIPPING ERROR MARKER IN HEADER: "+p[o])}else{m=m.replace(/^\s+/,"");h[g]=m}}else{i.push("PROBLEM LINE IN HEADER: "+p[o]);h[p[o]]=""}}t[""]=h}else{t[""]={}}return t};Gettext.prototype.parse_po_dequote=function(e){var t;if(t=e.match(/^"(.*)"/)){e=t[1]}e=e.replace(/\\"/g,'"');return e};Gettext.prototype.try_load_lang_json=function(e){var t=this.sjax(e);if(!t)return;var n=this.JSON(t);this.parse_locale_data(n);return 1};Gettext.prototype.get_lang_refs=function(){var e=new Array;var t=document.getElementsByTagName("link");for(var n=0;n<t.length;n++){if(t[n].rel=="gettext"&&t[n].href){if(typeof t[n].type=="undefined"||t[n].type==""){if(/\.json$/i.test(t[n].href)){t[n].type="application/json"}else if(/\.js$/i.test(t[n].href)){t[n].type="application/json"}else if(/\.po$/i.test(t[n].href)){t[n].type="application/x-po"}else if(/\.mo$/i.test(t[n].href)){t[n].type="application/x-mo"}else{throw new Error("LINK tag with rel=gettext found, but the type and extension are unrecognized.")}}t[n].type=t[n].type.toLowerCase();if(t[n].type=="application/json"){t[n].type="application/json"}else if(t[n].type=="text/javascript"){t[n].type="application/json"}else if(t[n].type=="application/x-po"){t[n].type="application/x-po"}else if(t[n].type=="application/x-mo"){t[n].type="application/x-mo"}else{throw new Error("LINK tag with rel=gettext found, but the type attribute ["+t[n].type+"] is unrecognized.")}e.push(t[n])}}return e};Gettext.prototype.textdomain=function(e){if(e&&e.length)this.domain=e;return this.domain};Gettext.prototype.gettext=function(e){var t;var n;var r;var i;return this.dcnpgettext(null,t,e,n,r,i)};Gettext.prototype.dgettext=function(e,t){var n;var r;var i;var s;return this.dcnpgettext(e,n,t,r,i,s)};Gettext.prototype.dcgettext=function(e,t,n){var r;var i;var s;return this.dcnpgettext(e,r,t,i,s,n)};Gettext.prototype.ngettext=function(e,t,n){var r;var i;return this.dcnpgettext(null,r,e,t,n,i)};Gettext.prototype.dngettext=function(e,t,n,r){var i;var s;return this.dcnpgettext(e,i,t,n,r,s)};Gettext.prototype.dcngettext=function(e,t,n,r,i){var s;return this.dcnpgettext(e,s,t,n,r,i,i)};Gettext.prototype.pgettext=function(e,t){var n;var r;var i;return this.dcnpgettext(null,e,t,n,r,i)};Gettext.prototype.dpgettext=function(e,t,n){var r;var i;var s;return this.dcnpgettext(e,t,n,r,i,s)};Gettext.prototype.dcpgettext=function(e,t,n,r){var i;var s;return this.dcnpgettext(e,t,n,i,s,r)};Gettext.prototype.npgettext=function(e,t,n,r){var i;return this.dcnpgettext(null,e,t,n,r,i)};Gettext.prototype.dnpgettext=function(e,t,n,r,i){var s;return this.dcnpgettext(e,t,n,r,i,s)};Gettext.prototype.dcnpgettext=function(e,t,n,r,i,s){if(!this.isValidObject(n))return"";var o=this.isValidObject(r);var u=this.isValidObject(t)?t+Gettext.context_glue+n:n;var a=this.isValidObject(e)?e:this.isValidObject(this.domain)?this.domain:"messages";var f="LC_MESSAGES";var s=5;var l=new Array;if(typeof Gettext._locale_data!="undefined"&&this.isValidObject(Gettext._locale_data[a])){l.push(Gettext._locale_data[a])}else if(typeof Gettext._locale_data!="undefined"){for(var c in Gettext._locale_data){l.push(Gettext._locale_data[c])}}var h=[];var p=false;var d;if(l.length){for(var v=0;v<l.length;v++){var m=l[v];if(this.isValidObject(m.msgs[u])){for(var g=0;g<m.msgs[u].length;g++){h[g]=m.msgs[u][g]}h.shift();d=m;p=true;if(h.length>0&&h[0].length!=0)break}}}if(h.length==0||h[0].length==0){h=[n,r]}var y=h[0];if(o){var b;if(p&&this.isValidObject(d.head.plural_func)){var w=d.head.plural_func(i);if(!w.plural)w.plural=0;if(!w.nplural)w.nplural=0;if(w.nplural<=w.plural)w.plural=0;b=w.plural}else{b=i!=1?1:0}if(this.isValidObject(h[b]))y=h[b]}return y};Gettext.strargs=function(e,t){if(null==t||"undefined"==typeof t){t=[]}else if(t.constructor!=Array){t=[t]}var n="";while(true){var r=e.indexOf("%");var i;if(r==-1){n+=e;break}n+=e.substr(0,r);if(e.substr(r,2)=="%%"){n+="%";e=e.substr(r+2)}else if(i=e.substr(r).match(/^%(\d+)/)){var s=parseInt(i[1]);var o=i[1].length;if(s>0&&t[s-1]!=null&&typeof t[s-1]!="undefined")n+=t[s-1];e=e.substr(r+1+o)}else{n+="%";e=e.substr(r+1)}}return n};Gettext.prototype.strargs=function(e,t){return Gettext.strargs(e,t)};Gettext.prototype.isArray=function(e){return this.isValidObject(e)&&e.constructor==Array};Gettext.prototype.isValidObject=function(e){if(null==e){return false}else if("undefined"==typeof e){return false}else{return true}};Gettext.prototype.sjax=function(e){var t;if(window.XMLHttpRequest){t=new XMLHttpRequest}else if(navigator.userAgent.toLowerCase().indexOf("msie 5")!=-1){t=new ActiveXObject("Microsoft.XMLHTTP")}else{t=new ActiveXObject("Msxml2.XMLHTTP")}if(!t)throw new Error("Your browser doesn't do Ajax. Unable to support external language files.");t.open("GET",e,false);try{t.send(null)}catch(n){return}var r=t.status;if(r==200||r==0){return t.responseText}else{var i=t.statusText+" (Error "+t.status+")";if(t.responseText.length){i+="\n"+t.responseText}alert(i);return}};Gettext.prototype.JSON=function(data){return eval("("+data+")")}