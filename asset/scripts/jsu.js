$(document).ready(function() {
	$('a[href="#"]').click(function(e) {e.preventDefault();});
	$('#event').click(function(id) {
		if($(this).attr("data-event") == 'cartDestroy') {
			$('.cart-count').html(0);
			/* We'll also make an AJAX call to the API here to actually empty the contents of the cart.
			 * rather than just use .html(0);
			 */
		}
	});
	$('#addToCart').click(function() {
		if($(this).attr("data-cart")) {
			$.get('ajax/addtocart.php?product_id=' + $(this).attr("data-cart") + '&qty=' + $(this).attr("data-qty"));
			$('#cartMsg').html('<span class="text text-success">Added to cart.</span>');
			setInterval(function(){
			     $('#cartMsg').fadeOut('fast');
			}, 3000);
		}
	});
	$('.getInfo').click(function() {
		if($(this).attr("data-lookup")) {
			$('#ajaxReturnModule').load('ajax/modal.php');
			$.get('ajax/info.php?d='+$(this).attr('data-lookup'), function(response, status, xhr) {
				if(status == "error") {
					if(xhr.status == "404") {
						$("body").html("<div class=\"modal error\">Oops, something went wrong. Please wait " +
							"while we fix it.<a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a></div>");
					}
				} else {
					$('#info').modal({
						keyboard: true,
						show: true
					});
					var data = jQuery.parseJSON(response);
					$('h4#modalLabel').html(data["info_title"]);
					$('p#main_modal_cntnt').html('<p>'+nl2br(data["info_data"])+'</p>');
					$('#timestamp').html(formatDate(new Date(data["info_last_updated"]), '%H:%m:%s</span> on <span class="text text-info">%d %M %Y'));
					$('#author').html(data["client_firstname"]+' '+data["client_lastname"]);
				}
			});
		}
	});
	$('.order').click(function() {
		if($(this).attr("data-order")) {
			$('#ajaxReturnModule').load('ajax/modal.php');
			$.get('ajax/vieworder.php?id='+$(this).attr('data-order'), function(response, status, xhr) {
				if(status == "error") {
					if(xhr.status == "404") {
						$("body").html("<div class=\"modal error\">Oops, something went wrong. Please wait " +
							"while we fix it.<a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a></div>");
					}
				} else {
					$('#info').modal({
						keyboard: true,
						show: true
					});
					var data = jQuery.parseJSON(response);
					$('h4#modalLabel').html(data[0]["order_id"]+' - '+data[0]["status_label"]);
					$('p#main_modal_cntnt').html('<p>'+unserialize(data[0]["order_data"])+'</p>');
					$('#timestamp').html(formatDate(new Date(data[0]["order_date"]), '%H:%m:%s</span> on <span class="text text-info">%d %M %Y'));
				}
			});
		}
	});
	$('.thumbnail').tooltip({
		placement : 'bottom',
		delay : { show : 50, hide : 500 }
	});
});

function getQty(qty) {
	$('#addToCart').attr("data-qty", qty);
}

function addToCart(id) {
	if(id == null) {
		return false;
	} else {
		alert(id);
	}
}

function nl2br (str, is_xhtml) {
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
}

function colon2br (str, is_xhtml) {
    return (str + '').replace(/([^>:: ,]?)(:: ,)/g, '$1' + '<br>' + '$2');
}

function formatDate(date, fmt) {
    function pad(value) {
        return (value.toString().length < 2) ? '0' + value : value;
    }
    return fmt.replace(/%([a-zA-Z])/g, function (_, fmtCode) {
        switch (fmtCode) {
        case 'Y':
            return date.getUTCFullYear();
        case 'M':
        	var d = pad(date.getMonth() + 1);
        	if(d.charAt(0) === '0') {
        		var e = d.slice( 1 );
        	}
        	var month=new Array();
        	month[01] ="Jan";
        	month[02] ="Feb";
        	month[03] ="Mar";
        	month[04] ="Apr";
        	month[05] ="May";
        	month[06] ="Jun";
        	month[07] ="Jul";
        	month[08] ="Aug";
        	month[09] ="Sep";
        	month[10]="Oct";
        	month[11]="Nov";
        	month[12]="Dec";
        	if(e > 9) {
        		return month[e];
        	} else {
        		return month[e];
        	}
        case 'd':
            return pad(date.getUTCDate());
        case 'H':
            return pad(date.getUTCHours());
        case 'm':
            return pad(date.getUTCMinutes());
        case 's':
            return pad(date.getUTCSeconds());
        default:
            throw new Error('Unsupported format code: ' + fmtCode);
        }
    });
}

function unserialize (data) {
	  // http://kevin.vanzonneveld.net
	  // +     original by: Arpad Ray (mailto:arpad@php.net)
	  // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
	  // +     bugfixed by: dptr1988
	  // +      revised by: d3x
	  // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +        input by: Brett Zamir (http://brett-zamir.me)
	  // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +     improved by: Chris
	  // +     improved by: James
	  // +        input by: Martin (http://www.erlenwiese.de/)
	  // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +     improved by: Le Torbi
	  // +     input by: kilops
	  // +     bugfixed by: Brett Zamir (http://brett-zamir.me)
	  // +      input by: Jaroslaw Czarniak
	  // +     improved by: Eli Skeggs
	  // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
	  // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
	  // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
	  // *       returns 1: ['Kevin', 'van', 'Zonneveld']
	  // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
	  // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
	  var that = this,
	    utf8Overhead = function (chr) {
	      // http://phpjs.org/functions/unserialize:571#comment_95906
	      var code = chr.charCodeAt(0);
	      if (code < 0x0080) {
	        return 0;
	      }
	      if (code < 0x0800) {
	        return 1;
	      }
	      return 2;
	    },
	    error = function (type, msg, filename, line) {
	      throw new that.window[type](msg, filename, line);
	    },
	    read_until = function (data, offset, stopchr) {
	      var i = 2, buf = [], chr = data.slice(offset, offset + 1);

	      while (chr != stopchr) {
	        if ((i + offset) > data.length) {
	          error('Error', 'Invalid');
	        }
	        buf.push(chr);
	        chr = data.slice(offset + (i - 1), offset + i);
	        i += 1;
	      }
	      return [buf.length, buf.join('')];
	    },
	    read_chrs = function (data, offset, length) {
	      var i, chr, buf;

	      buf = [];
	      for (i = 0; i < length; i++) {
	        chr = data.slice(offset + (i - 1), offset + i);
	        buf.push(chr);
	        length -= utf8Overhead(chr);
	      }
	      return [buf.length, buf.join('')];
	    },
	    _unserialize = function (data, offset) {
	      var dtype, dataoffset, keyandchrs, keys, contig,
	        length, array, readdata, readData, ccount,
	        stringlength, i, key, kprops, kchrs, vprops,
	        vchrs, value, chrs = 0,
	        typeconvert = function (x) {
	          return x;
	        };

	      if (!offset) {
	        offset = 0;
	      }
	      dtype = (data.slice(offset, offset + 1)).toLowerCase();

	      dataoffset = offset + 2;

	      switch (dtype) {
	        case 'i':
	          typeconvert = function (x) {
	            return parseInt(x, 10);
	          };
	          readData = read_until(data, dataoffset, ';');
	          chrs = readData[0];
	          readdata = readData[1];
	          dataoffset += chrs + 1;
	          break;
	        case 'b':
	          typeconvert = function (x) {
	            return parseInt(x, 10) !== 0;
	          };
	          readData = read_until(data, dataoffset, ';');
	          chrs = readData[0];
	          readdata = readData[1];
	          dataoffset += chrs + 1;
	          break;
	        case 'd':
	          typeconvert = function (x) {
	            return parseFloat(x);
	          };
	          readData = read_until(data, dataoffset, ';');
	          chrs = readData[0];
	          readdata = readData[1];
	          dataoffset += chrs + 1;
	          break;
	        case 'n':
	          readdata = null;
	          break;
	        case 's':
	          ccount = read_until(data, dataoffset, ':');
	          chrs = ccount[0];
	          stringlength = ccount[1];
	          dataoffset += chrs + 2;

	          readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
	          chrs = readData[0];
	          readdata = readData[1];
	          dataoffset += chrs + 2;
	          if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
	            error('SyntaxError', 'String length mismatch');
	          }
	          break;
	        case 'a':
	          readdata = {};

	          keyandchrs = read_until(data, dataoffset, ':');
	          chrs = keyandchrs[0];
	          keys = keyandchrs[1];
	          dataoffset += chrs + 2;

	          length = parseInt(keys, 10);
	          contig = true;

	          for (i = 0; i < length; i++) {
	            kprops = _unserialize(data, dataoffset);
	            kchrs = kprops[1];
	            key = kprops[2];
	            dataoffset += kchrs;

	            vprops = _unserialize(data, dataoffset);
	            vchrs = vprops[1];
	            value = vprops[2];
	            dataoffset += vchrs;

	            if (key !== i)
	              contig = false;

	            readdata[key] = value;
	          }
	          
	          if (contig) {
	            array = new Array(length);
	            for (i = 0; i < length; i++)
	              array[i] = readdata[i];
	            readdata = array;
	          }

	          dataoffset += 1;
	          break;
	        default:
	          error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
	          break;
	      }
	      return [dtype, dataoffset - offset, typeconvert(readdata)];
	    }
	  ;

	  return _unserialize((data + ''), 0)[2];
	}