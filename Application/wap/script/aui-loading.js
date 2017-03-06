/**
 * aui-loading.js LOADING
 * verson 0.0.1
 * @author 流浪男 && Beck
 * http://www.auicss.com
 * @todo more things to abstract, e.g. Loading css etc.
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
(function(window) {
	"use strict";
	var offsetX,offsetY;
	var loadingWrap;
	var isLoading = false;
	function Loading (options) {
		 this._init(options);
	}
	Loading.prototype.options = {
		width:'60',
		height:'60',
		offsetX:'', //水平偏移量
		offsetY:'', //垂直偏移量
		style:'1',
		delay:'300'
	};
	Loading.prototype._init = function(options) {
		extend(this.options, options);
		if(!this.options.src){
			return;
		}
	};
	Loading.prototype.show =function(options) {
		offsetX = this.options.offsetX ? this.options.offsetX : -(this.options.width/2);
		offsetY = this.options.offsetY ? this.options.offsetY : -(this.options.height/2);
		var style = this.options.style ? this.options.style : 1;
		if(style == 1){
			// var svgHtml = '<svg width="'+this.options.width+'px" height="'+this.options.height+'px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-clock"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><circle cx="50" cy="50" r="30" fill="#f1c40f" stroke="#996600" stroke-width="8px"></circle><line x1="50" y1="50" x2="50" y2="30" stroke="#ffffff" stroke-width="5" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="2s" repeatCount="indefinite"></animateTransform></line><line x1="50" y1="50" x2="50" y2="20" stroke="" stroke-width="2px" stroke-linecap="round" opacity="0"><animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="0.4s" repeatCount="indefinite"></animateTransform></line></svg>';
			var svgHtml = '<svg width="'+this.options.width+'px" height="'+this.options.height+'px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><circle cx="50" cy="50" r="40" stroke="#e6e6e6" fill="none" stroke-width="10" stroke-linecap="round"></circle><circle cx="50" cy="50" r="40" stroke="#3492e9" fill="none" stroke-width="6" stroke-linecap="round"><animate attributeName="stroke-dashoffset" dur="3s" repeatCount="indefinite" from="0" to="502"></animate><animate attributeName="stroke-dasharray" dur="3s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"></animate></circle></svg>';
		}else{
			var svgHtml = '<svg width="'+this.options.width+'px" height="'+this.options.height+'px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-poi"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><g transform="translate(50,45)"><g><g transform="translate(-50,-45)"><path d="M77.5,45.3c0-15.3-12.3-27.8-27.5-27.8S22.5,29.9,22.5,45.3c0,13.3,9.3,24.5,21.7,27.1L50,82.5l5.8-10.1 C68.2,69.7,77.5,58.6,77.5,45.3z M50.3,55.4c-5.4,0-9.7-4.4-9.7-9.8c0-5.4,4.3-9.8,9.7-9.8s9.7,4.4,9.7,9.8 C60,51,55.7,55.4,50.3,55.4z" fill="#3492e9"><animateTransform attributeName="transform" type="translate" dur="1s" repeatCount="indefinite" from="0,17" to="0,17" values="0,17;0,-17;0,17" keyTimes="0;0.5;1" keySplines="0.4 0.8 0.4 0.8;0.8 0.4 0.8 0.4" calcMode="spline"></animateTransform></path></g><animateTransform attributeName="transform" type="none" from="0" to="360" dur="1s" repeatCount="indefinite"></animateTransform></g></g></svg>';
		}
		loadingWrap = document.createElement("div");
		loadingWrap.setAttribute("id", "loading");
		loadingWrap.style.width = this.options.width+"px";
		loadingWrap.style.height = this.options.height+"px";
		loadingWrap.style.position = "fixed";
		loadingWrap.style.left = "50%";
		loadingWrap.style.top = "50%";
		loadingWrap.style.zIndex = "999";
		loadingWrap.style.marginLeft = offsetX+"px";
		loadingWrap.style.marginTop = offsetY+"px";
		loadingWrap.innerHTML = svgHtml;
		if(!document.getElementById('loading')){
			document.body.appendChild(loadingWrap);
			isLoading = true;
			window.addEventListener("touchmove", function(event){
				if(isLoading){
					event.preventDefault();
				}

			},false)
		}else if(document.getElementById('loading')){
			var loadingDom = document.getElementById('loading');
			loadingDom.parentNode.removeChild(loadingDom);
		}
	}
	Loading.prototype.hide = function (options) {
		setTimeout(function(){
			if(document.getElementById('loading') && document.getElementById('loading').parentNode){
				var loadingDom = document.getElementById('loading');
	            loadingDom.parentNode.removeChild(loadingDom);
	            isLoading = false;

	        }
        }, this.options.delay)
	}
	function extend (a, b) {
		for (var key in b) {
		  	if (b.hasOwnProperty(key)) {
		  		a[key] = b[key];
		  	}
	  	}
	  	return a;
	}
	window.auiLoading = Loading;
})(window);