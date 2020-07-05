"use strict";

window.addEventListener("load", (_event) => {
	document.getElementById("input-url").addEventListener("change", update_output);
	document.getElementById("input-url").addEventListener("keyup", update_output);
	document.getElementById("input-url").addEventListener("input", update_output);
	
	document.getElementById("input-language").addEventListener("change", update_output);
	document.getElementById("input-language").addEventListener("keyup", update_output);
	document.getElementById("input-language").addEventListener("input", update_output);
	
	document.getElementById("input-theme").addEventListener("change", update_output);
	document.getElementById("input-theme").addEventListener("input", update_output);
	
	document.getElementById("input-theme-dark").addEventListener("change", update_output);
	document.getElementById("input-theme-dark").addEventListener("input", update_output);
	
	update_output();
});

function update_output() {
	let url = document.getElementById("input-url").value;
	let lang = document.getElementById("input-language").value;
	let root_url = document.getElementById("root-url").value;
	let theme = document.getElementById("input-theme").value;
	let theme_dark = document.getElementById("input-theme-dark").value;
	
	if(url.trim().length == 0) return;
	
	document.getElementById("output-markdown").value = generate_markdown(url, lang);
	document.getElementById("output-html").value = generate_html(root_url, url, lang, theme, theme_dark);
	document.getElementById("preview").src = generate_iframe_src(root_url, url, lang, theme, theme_dark);
}

function generate_markdown(url, lang) {
	return `[${get_filename_url(url)}](${url}) (${lang})`;
}

function generate_html(root_url, url, lang, theme, theme_dark) {
	let iframe_src = generate_iframe_src(root_url, url, lang, theme, theme_dark);
	return `<iframe src="${iframe_src.replace(/&/g, "&amp;")}" style="width: 100%; resize: vertical;" /></iframe>`
}

function generate_iframe_src(root_url, url, lang, theme, theme_dark) {
	let iframe_src = `${root_url}?action=view&url=${encodeURIComponent(url)}`;
	if(lang.length > 0) iframe_src += `&lang=${encodeURIComponent(lang)}`;
	if(theme !== "__AUTO__") iframe_src += `&theme=${encodeURIComponent(theme)}`;
	if(theme_dark !== "__AUTO__") iframe_src += `&theme-dark=${encodeURIComponent(theme_dark)}`;
	return iframe_src;
}

function get_filename_url(url) {
	let slashpos = url.lastIndexOf("/");
	if(slashpos == -1) return url;
	return url.substring(slashpos + 1);
}
