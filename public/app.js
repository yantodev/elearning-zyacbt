	function pesan_err(pesan){
        var temp = '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-info"></i> Perhatian</h4>'+pesan+'</div>'
        return temp;
    }
    
	function pesan_succ(pesan){
        var temp = '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-info"></i> Informasi</h4>'+pesan+'</div>';
        return temp;
    }

    // Tambahkan token CSRF ke seluruh request AJAX POST secara terpusat.
    if (window.jQuery && $('meta[name="csrf-token-name"]').length) {
        var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfTokenValue = $('meta[name="csrf-token"]').attr('content');

        // Form POST biasa juga harus membawa token, bukan hanya request AJAX.
        $('form').each(function () {
            if (!$(this).find('input[name="' + csrfTokenName + '"]').length) {
                $('<input>', {
                    type: 'hidden',
                    name: csrfTokenName,
                    value: csrfTokenValue
                }).appendTo(this);
            }
        });

        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            if (String(options.type || options.method || 'GET').toUpperCase() !== 'POST') {
                return;
            }

            var tokenName = csrfTokenName;
            var tokenValue = csrfTokenValue;

            if (window.FormData && options.data instanceof FormData) {
                if (!options.data.has(tokenName)) {
                    options.data.append(tokenName, tokenValue);
                }
            } else if (typeof options.data === 'string') {
                options.data += (options.data ? '&' : '') + encodeURIComponent(tokenName) + '=' + encodeURIComponent(tokenValue);
            } else {
                options.data = options.data || {};
                options.data[tokenName] = tokenValue;
            }
        });
    }
	
