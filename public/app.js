	function pesan_err(pesan){
        var temp = '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-info"></i> Perhatian</h4>'+pesan+'</div>'
        return temp;
    }
    
	function pesan_succ(pesan){
        var temp = '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-info"></i> Informasi</h4>'+pesan+'</div>';
        return temp;
    }

	// Tambahkan token CSRF ke seluruh request AJAX POST secara terpusat.
	// Token dibaca ulang dari cookie karena server dapat memperbaruinya setelah
	// request sebelumnya. Ini penting untuk navigasi soal dan simpan jawaban.
	if (window.jQuery && $('meta[name="csrf-token-name"]').length) {
		var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
		var csrfTokenValue = $('meta[name="csrf-token"]').attr('content');
		var csrfCookieName = $('meta[name="csrf-cookie-name"]').attr('content') || '';

		function readCookie(name) {
			var prefix = encodeURIComponent(name) + '=';
			var cookies = document.cookie ? document.cookie.split(';') : [];

			for (var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i].replace(/^\s+/, '');
				if (cookie.indexOf(prefix) === 0) {
					return decodeURIComponent(cookie.substring(prefix.length));
				}
			}

			return '';
		}

		function syncCsrfToken() {
			var cookieToken = csrfCookieName ? readCookie(csrfCookieName) : '';
			if (cookieToken) {
				csrfTokenValue = cookieToken;
			}

			$('input[name="' + csrfTokenName + '"]').val(csrfTokenValue);
		}

		function ensureCsrfInputs() {
			syncCsrfToken();
			$('form').each(function () {
				if (!$(this).find('input[name="' + csrfTokenName + '"]').length) {
					$('<input>', {
						type: 'hidden',
						name: csrfTokenName,
						value: csrfTokenValue
					}).appendTo(this);
				}
			});
		}

		$(ensureCsrfInputs);

		$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
			if (String(options.type || options.method || 'GET').toUpperCase() !== 'POST') {
				return;
			}

			syncCsrfToken();
			var tokenValue = encodeURIComponent(csrfTokenValue);
			var tokenName = encodeURIComponent(csrfTokenName).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
			var tokenParameter = encodeURIComponent(csrfTokenName) + '=' + tokenValue;

			if (window.FormData && options.data instanceof FormData) {
				if (options.data.set) {
					options.data.set(csrfTokenName, csrfTokenValue);
				} else {
					options.data.append(csrfTokenName, csrfTokenValue);
				}
			} else if (typeof options.data === 'string') {
				var tokenPattern = new RegExp('(^|&)' + tokenName + '=[^&]*');
				if (tokenPattern.test(options.data)) {
					options.data = options.data.replace(tokenPattern, '$1' + tokenParameter);
				} else {
					options.data += (options.data ? '&' : '') + tokenParameter;
				}
			} else {
				options.data = options.data || {};
				options.data[csrfTokenName] = csrfTokenValue;
			}

			jqXHR.always(function () {
				ensureCsrfInputs();
			});
		});
	}
	
