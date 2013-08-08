/* LightZAP v2.54 (silent update2)
by Szalai Mihaly - http://dragonzap.szunyi.com
original by Lokesh Dhakar (lightbox) - http://lokeshdhakar.com

For more information, visit:
http://dragonzap.szunyi.com/index.php?e=page&al=lightzap&l=en

Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
- free for use in both personal and commercial projects
- attribution requires leaving author name, author link, and the license info intact

Thanks
- Scott Upton(uptonic.com), Peter-Paul Koch(quirksmode.com), and Thomas Fuchs(mir.aculo.us) for ideas, libs, and snippets.
- Artemy Tregubenko (arty.name) for cleanup and help in updating to latest proto-aculous in v2.05.
- Szalai Mihaly (dragonzap.szunyi.com), automatic image resize for screen, fullscreen viewer,  print button, download button, like button and new design.*/

var imgNotFound,
//----- Options --------
	imagetext	= "",	//"Image "
	oftext		= " / ",//" of "
	bytext		= "by",	//"by"
	notfoundtext	= "Image not found",
	print		= false,
	download	= false,
	like		= true;
//----------------------

var $lightzap, $container, $image, $lbContainer;
var windowWidth, windowHeight, originalWidth, originalHeight, album = [], currentImageIndex = 0, isfull = false, marginWidth = -1, marginHeight = -1;
var pfx = ["webkit", "moz", "ms", "o", ""];

function getElementsByClassName(element, className) {
	if (element.getElementsByClassName)
		return element.getElementsByClassName(className);
	else
		return document.querySelectorAll('.' + className);
};

function lzInit()
{
	//Set links
	var _ref = document.links, i = _ref.length;
	while (i--)
	{
		var a = _ref[i];
		if (a.getAttribute("data-lightzap") != null)
			a.onclick = function ()	{return lzStart(this);};
	}
	
	//FullScreen
	var pfx0 = ["IsFullScreen", "FullScreen"], pfx1 = ["CancelFullScreen", "RequestFullScreen"];
	var k, m, t = "undefined", p = 0;
	while (p < pfx.length && !document[m])
	{
		k = 0;
		while (k < pfx0.length)
		{
			m = pfx0[k];
			if (pfx[p] == "")
			{
				m = m.substr(0, 1).toLowerCase() + m.substr(1);
				pfx1[0] = pfx1[0].substr(0, 1).toLowerCase() + pfx1[0].substr(1);
				pfx1[1] = pfx1[1].substr(0, 1).toLowerCase() + pfx1[1].substr(1);
			}
			m = pfx[p] + m;
			t = typeof document[m];
			if (t != "undefined")
			{
				pfx = [pfx[p] + pfx1[0], pfx[p] + pfx1[1], m];
				p = 2;
				break;
			}
			k++;
		}
		p++;
	}

	//Build lightzap
	$lightzap = document.createElement("div");
	$lightzap.id = "lightzap";
	$lightzap.style.display = 'none';
	document.body.appendChild($lightzap);

	var tmp = document.createElement("div");
	tmp.className = "lz-bg";
	tmp.onclick = lzEnd;
	$lightzap.appendChild(tmp);

	$container = document.createElement("div");
	$container.className = "lz-container";
	$container.style.display = 'none';
	$lightzap.appendChild($container);

	tmp = document.createElement("div");
	tmp.className = "lz-loader";
	$container.appendChild(tmp);

	$image = document.createElement("img");
	$image.className = "lz-image";
	$container.appendChild($image);

	var group = document.createElement("div");
	group.className = "lz-nav";
	$container.appendChild(group);

	tmp = document.createElement("a");
	tmp.className = "lz-prev";
	tmp.onclick = function ()
	{
		lzChangeImage(currentImageIndex + 1);
		return false;
	};
	group.appendChild(tmp);

	tmp = document.createElement("a");
	tmp.className = "lz-next";
	tmp.onclick = function ()
	{
		lzChangeImage(currentImageIndex - 1);
		return false;
	};
	group.appendChild(tmp);

	group = document.createElement("div");
	group.className = "lz-buttonContainer";
	$container.appendChild(group);

	tmp = document.createElement("a");
	tmp.className = "lz-button lz-more";
	tmp.onclick = function ()
	{
		if (getElementsByClassName($container, "lz-desc")[0].style.display == 'none') getElementsByClassName($container, "lz-desc")[0].style.display = '';
		else getElementsByClassName($container, "lz-desc")[0].style.display = 'none';
		return false;
	};
	group.appendChild(tmp);

	tmp = document.createElement("a");
	tmp.className = "lz-button lz-print";
	tmp.onclick = lzPrint;
	group.appendChild(tmp);

	tmp = document.createElement("a");
	tmp.className = "lz-button lz-download";
	tmp.onclick = lzDownload;
	tmp.target = "_blank";
	group.appendChild(tmp);

	tmp = document.createElement("a");
	tmp.className = "lz-button lz-like";
	tmp.onclick = lzLike;
	group.appendChild(tmp);

	//Buttons
	if (p != 3) pfx = false;
	else
	{
		tmp = document.createElement("div");
		tmp.className = "lz-button lz-fullScreen";
		tmp.onclick = function ()
		{
			if (isfull) document[pfx[0]]();
			else $lightzap[pfx[1]]();
		};
		group.appendChild(tmp);
	}

	tmp = document.createElement("a");
	tmp.className = "lz-button lz-close";
	tmp.onclick = lzEnd;
	group.appendChild(tmp);

	$lbContainer = document.createElement("div");
	$lbContainer.className = "lz-labelContainer";
	$container.appendChild($lbContainer);

	tmp = document.createElement("div");
	tmp.className = "lz-float lz-caption";
	$lbContainer.appendChild(tmp);

	tmp = document.createElement("div");
	tmp.className = "lz-float lz-desc";
	tmp.style.display = 'none';
	$lbContainer.appendChild(tmp);

	tmp = document.createElement("div");
	tmp.className = "lz-float lz-resolution";
	$lbContainer.appendChild(tmp);

	tmp = document.createElement("a");
	tmp.className = "lz-float lz-by";
	tmp.target = "_blank";
	$lbContainer.appendChild(tmp);

	tmp = document.createElement("div");
	tmp.className = "lz-float lz-number";
	$lbContainer.appendChild(tmp);
};
window.onload = lzInit;

function lzStart($link)
{
	//Show overlay
	lzShowOthers(false);
	lzSizeOverlay();
	$lightzap.style.display = '';
	$container.style.display = '';
	window.onresize = lzSizeOverlay;

	//Get original margin
	if (marginWidth == -1)
	{
		imgNotFound = window.getComputedStyle($image, "").getPropertyValue("background-image").replace("url(", "").replace(")", "").replace('"', '').replace('"', '');
		$image.style.backgroundImage = "none";
		var tmp = window.getComputedStyle($container, "");
		marginHeight = parseInt(tmp.getPropertyValue("margin-top")) + parseInt(tmp.getPropertyValue("margin-bottom")) + parseInt(tmp.getPropertyValue("padding-top")) + parseInt(tmp.getPropertyValue("padding-bottom")) + parseInt(tmp.getPropertyValue("border-top-width")) + parseInt(tmp.getPropertyValue("border-bottom-width"));
		marginWidth = parseInt(tmp.getPropertyValue("margin-left")) + parseInt(tmp.getPropertyValue("margin-right")) + parseInt(tmp.getPropertyValue("padding-left")) + parseInt(tmp.getPropertyValue("padding-right")) + parseInt(tmp.getPropertyValue("border-left-width")) + parseInt(tmp.getPropertyValue("border-right-width"));		
	}

	//Create album
	album = [];
	var a, i, _len, _ref, imageNumber = 0;
	if ($link.getAttribute("data-lightzap") == "")
		lzReadAlbum($link);
	else
	{
		var _ref = document.links, _href = $link.href, _attr = $link.getAttribute("data-lightzap"), i = _ref.length, j = 0;
		while (i--)
		{
			var a = _ref[i];
			if (a.getAttribute("data-lightzap") != null && a.getAttribute("data-lightzap") == _attr)
			{
				lzReadAlbum(a);
				if (a.href == $link.href) imageNumber = j;
				j++;
			}
		}
	}
	lzChangeImage(imageNumber);
	return false;
};

function lzReadAlbum($link)
{
	var download = false, like = false, print = false, options = $link.getAttribute("data-options");
	if (options != null && options.length > 3)
	{
		download = options.indexOf("download") != -1;
		like = options.indexOf("like") != -1;
		print = options.indexOf("print") != -1;
	}
	album.push({
		link: $link.getAttribute("href"),
		title: $link.getAttribute("title"),
		desc: $link.getAttribute("data-desc"),
		by: $link.getAttribute("data-by"),
		by_link: $link.getAttribute("data-link"),
		download: download,
		print: print,
		like: like
	});
};

function lzChangeImage(imageNumber)
{
	//Hide other
	document.onkeypress = lzKeyboardAction;
	getElementsByClassName($container, "lz-loader")[0].style.display = "";
	getElementsByClassName($container, "lz-nav")[0].style.display = "none";
	getElementsByClassName($container, "lz-buttonContainer")[0].style.display = "none";
	$lbContainer.style.display = "none";
	$image.className = "lz-hide";
	
	//New image
	var preloader = new Image;
	preloader.onload = function ()
	{
		$image.src = album[imageNumber].link;
		originalWidth = preloader.width;
		originalHeight = preloader.height;
		currentImageIndex = imageNumber;
		return lzGetImageSize();
	};
	preloader.onerror = function ()
	{
		album[imageNumber].title = notfoundtext;
		album[imageNumber].link = imgNotFound;
		$image.src = album[imageNumber].link;
		originalWidth = 256;
		originalHeight = 256;
		currentImageIndex = imageNumber;
		return lzGetImageSize();
	};
	preloader.src = album[imageNumber].link;
};

function lzSizeOverlay()
{
	var _windowWidth, _windowHeight;
	if(typeof(window.innerWidth) == 'number') //Non-IE
	{
		_windowWidth = Math.min(window.innerWidth, document.body.clientWidth);
		_windowHeight = window.innerHeight;
	}
	else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) //IE 6+ in 'standards compliant mode'
	{
		_windowWidth = document.documentElement.clientWidth;
		_windowHeight = document.documentElement.clientHeight;
	}
	else if (document.body && (document.body.clientWidth || document.body.clientHeight)) //IE 4 compatible
	{
		_windowWidth = document.body.clientWidth;
		_windowHeight = document.body.clientHeight;
	}

	//If chanced size
	if (windowWidth != _windowWidth || windowHeight != _windowHeight)
	{
		//Set size
		windowWidth = _windowWidth;//(_windowWidth <= screen.width) ? _windowWidth : screen.width * 0.8;
		windowHeight = _windowHeight;//(_windowHeight <= screen.height) ? _windowHeight : screen.height * 0.8;
		$lightzap.style.width = _windowWidth;
		$lightzap.style.height = _windowHeight;

		//Is fullscreen?
		isfull = false;
		if (pfx != false) isfull = (typeof document[pfx[2]] == "function" ? document[pfx[2]]() : document[pfx[2]]);

		if (!isfull) isfull = (windowWidth >= screen.width * 0.99 && windowHeight >= screen.height * 0.99);

		//Set style
		if (isfull)
		{
			$lightzap.className = "full-screen";
			$lightzap.style.width = "";
			$lightzap.style.height = "";
			$container.style.height = "100%";
			getElementsByClassName($lightzap, "lz-bg")[0].onclick = null;
		}
		else
		{
			getElementsByClassName($lightzap, "lz-bg")[0].onclick = lzEnd
			$lightzap.className = "";
			$container.style.width = "";
			$container.style.height = "";
		}

		//Update image size
		if (album.length > 0)
			lzGetImageSize();
	}
};

function lzGetImageSize()
{
	//Sizes
	var placeWidth = windowWidth, placeHeight = windowHeight, imageWidth = originalWidth, imageHeight = originalHeight;
	var master = $container.style, slave = $image.style;
	if (!isfull)
	{
		placeWidth -= marginWidth;
		placeHeight -= marginHeight;
	}
	else
	{
		slave = master;
		master = $image.style;
		if (pfx)
		{
			placeWidth = screen.width;
			placeHeight = screen.height;
		}
	}

	//Calculate optional size
	if (imageWidth > placeWidth)
	{
		imageHeight = (placeWidth * imageHeight) / imageWidth;
		imageWidth = placeWidth;
	}
	if (imageHeight > placeHeight)
	{
		imageWidth = (placeHeight * imageWidth) / imageHeight;
		imageHeight = placeHeight;
	}
	
	//Set box style
	slave.top = "0";
	slave.left = "0";
	slave.width = "";
	slave.height = "";
	master.top = (placeHeight - imageHeight) * 0.5 + "px";
	master.left = (placeWidth - imageWidth) * 0.5 + "px";
	master.width = imageWidth + "px";
	master.height = imageHeight + "px";
	lzShowImage();
};

function lzShowImage()
{
	lzUpdateNav();
	lzUpdateDetails();

	//Preload
	var preloadNext, preloadPrev;
	if (album.length > currentImageIndex + 1)
	{
		preloadNext = new Image;
		preloadNext.src = album[currentImageIndex + 1].link;
	}
	if (currentImageIndex > 0)
	{
		preloadPrev = new Image;
		preloadPrev.src = album[currentImageIndex - 1].link;
	}
	
	setTimeout(function(){
		$image.className = "lz-image";
		getElementsByClassName($container, "lz-nav")[0].style.display = "";
		$lbContainer.style.display = "";
		getElementsByClassName($container, "lz-buttonContainer")[0].style.display = "";
		getElementsByClassName($container, "lz-loader")[0].style.display = "none";
	},380);
};

function lzUpdateDetails()
{
	//Counter
	var element = getElementsByClassName($lbContainer, "lz-number")[0];
	if (album.length > 1)
	{
		element.textContent = imagetext + (album.length - currentImageIndex) + oftext + album.length;
		element.style.display = "";
	}
	else element.style.display = "none";

	//Caption
	element = getElementsByClassName($lbContainer, "lz-caption")[0];
	if (album[currentImageIndex].title != null && album[currentImageIndex].title != "")
	{
		element.textContent = album[currentImageIndex].title;
		element.style.display = "";
		if (album[currentImageIndex].title == notfoundtext) return false;
	}
	else element.style.display = "none";

	//Description
	element = getElementsByClassName($container, "lz-more")[0];
	if (album[currentImageIndex].desc != null && album[currentImageIndex].desc != "")
	{
		element.style.display = "";
		element = getElementsByClassName($lbContainer, "lz-desc")[0];
		element.innerHTML = album[currentImageIndex].desc;
		element.style.display = "none";
	}
	else
	{
		element.style.display = "none";
		getElementsByClassName($lbContainer, "lz-desc")[0].style.display = "none";
	}

	//Author
	element = getElementsByClassName($lbContainer, "lz-by")[0];
	if (album[currentImageIndex].by != null && album[currentImageIndex].by != "")
	{
		element.innerHTML = bytext + " <span>" + album[currentImageIndex].by + "</span>";
		element.style.display = "";
		if (album[currentImageIndex].by_link != null && album[currentImageIndex].by_link != "") element.href = album[currentImageIndex].by_link;
	}
	else
	{
		element.innerHTML = "";
		element.style.display = "none";
		if (album[currentImageIndex].by_link != null && album[currentImageIndex].by_link != "") getElementsByClassName($container, ".lz-caption").html() + '</a>';
	}

	//Others
	getElementsByClassName($container, "lz-like")[0].style.display = (album[currentImageIndex].like != like) ? "" : "none";
	getElementsByClassName($container, "lz-download")[0].style.display = (album[currentImageIndex].download != download) ? "" : "none";
	getElementsByClassName($container, "lz-print")[0].style.display = (album[currentImageIndex].print != print) ? "" : "none";

	getElementsByClassName($lbContainer, "lz-resolution")[0].textContent = originalWidth + " x " + originalHeight;
};

function lzUpdateNav()
{
	getElementsByClassName($container, "lz-prev")[0].style.display = "none";
	getElementsByClassName($container, "lz-next")[0].style.display = "none";
	if (currentImageIndex > 0) getElementsByClassName($container, "lz-next")[0].style.display = "";

	if (currentImageIndex < album.length - 1) getElementsByClassName($container, "lz-prev")[0].style.display = "";
};

function lzKeyboardAction(e)
{
	var keycode = e.keyCode, key = String.fromCharCode(e.charCode).toLowerCase(),
		KEYCODE_ESC = 27,
		KEYCODE_LEFTARROW = 37,
		KEYCODE_RIGHTARROW = 39,
		KEYCODE_F11 = 122;
	if (keycode == KEYCODE_ESC || key.match(/x|o|c/)) lzEnd();
	else if (key == "n" || keycode == KEYCODE_RIGHTARROW)
	{
		if (currentImageIndex != 0) lzChangeImage(currentImageIndex - 1);
	}
	else if ((key == "p" || keycode == KEYCODE_LEFTARROW) && currentImageIndex != album.length - 1) lzChangeImage(currentImageIndex + 1);
};

function lzPrint()
{
	win = window.open();
	self.focus();
	win.document.open();
	win.document.write("<html><body stlye='margin:0 auto;padding:0;'><h1 style='margin:0 0 0.48em;'>" + getElementsByClassName($container, "lz-caption")[0].textContent + "</h1><div style='text-align:center;'><img src='" + album[lz.currentImageIndex].link + "' style='max-width:100%;max-height:100%;'/></div><div style='text-align:right;'><i>" + getElementsByClassName($container, "lz-by")[0].textContent + "</i></div></body></html>");
	win.document.close();
	win.print();
	win.close();
};

function lzLike()
{
	if (!window.focus) return true;
		window.open("http://www.facebook.com/sharer/sharer.php?u=" + $image.src, "", 'width=400,height=200,scrollbars=yes');
};

function lzDownload()
{
	if (window.webkitURL)
	{ //Webkit
		var xhr = new XMLHttpRequest();
		xhr.open("GET", album[currentImageIndex].link);
		xhr.responseType = "blob";
		xhr.onreadystatechange = function ()
		{
			var a = document.createElement("a");
			a.href = (window.URL) ? window.URL.createObjectURL(xhr.response) : window.webkitURL.createObjectURL(xhr.response);
			a.download = album[currentImageIndex].link.substring(album[currentImageIndex].link.lastIndexOf("/") + 1);
			var e = document.createEvent("MouseEvents");
			e.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
			a.dispatchEvent(e);
		};
		xhr.send();
		return true;
	}
	else if (navigator.appName == 'Microsoft Internet Explorer')
	{ //IE
		win = window.open(album[currentImageIndex].link);
		self.focus();
		win.document.execCommand("SaveAs");
		win.close();
		return true;
	}
	else
	{ //Opera & Firefox (CANVAS)
		var canvas = document.createElement("canvas");
		document.body.appendChild(canvas);
		if (typeof canvas.getContext != "undefined")
		{
			try
			{
				var context = canvas.getContext("2d");
				canvas.width = Math.min(originalWidth, 1024);
				canvas.height = Math.min(originalHeight, originalHeight / originalWidth * 1024);
				canvas.style.width = canvas.width + "px";
				canvas.style.height = canvas.height + "px";
				context.drawImage($image, 0, 0, canvas.width, canvas.height);
				document.location.href = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
				document.body.removeChild(canvas);
				return true;
			}
			catch (err)
			{
				document.body.removeChild(canvas);
			}
		}
	}
	alert("Sorry, can't download");
};

function lzEnd()
{
	if (isfull && pfx != false) document[pfx[0]]();
	album = [];
	document.onkeypress = null;
	window.onresize = null;
	$lightzap.style.display = 'none';
	$container.style.display = 'none';
	lzShowOthers(true);
	return false;
};

function lzShowOthers(show)
{
	var _ref, i, tagNames = ["select", "object", "embeds"], tagNum = 3;
	show = (show) ? "visible" : "hidden";
	while(tagNum--)
	{
		_ref = document.getElementsByTagName(tagNames[tagNum]);
		i = _ref.length;
		while (i--)
			_ref[i].style.visibility = show;
	}
}
