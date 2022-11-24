/* cms/tags/assets/UBOW.TagsPicker.20220101.js
***********************************************/
UBOW.TagsPicker = function(el, options) {
	var my=this;
	var $el = $(el);
	var $tag_choices = $('#tags-select .tagcloud');
	var $form = $('#dlg-tags-picker form');
	var defaults = {
		on: [],
		verbose:!1
		};

	function init() {
		my.settings = $.extend({}, defaults, options);
		if (my.settings.verbose) UBOW.dump(my.settings,'TagsPicker.settings');
		$.each(my.settings.on, function(i,id) {
			$tag_choices.find('[tag=' + id + ']').toggleClass('on');
			});
		$tag_choices.find('i.on').each(function() {
			$el.append(this.outerHTML);
			});
		}

	$tag_choices.on('click', 'i', function(e){
		$(this).toggleClass('on');
		$el.empty();
		$tag_choices.find('i.on').each(function() {
			$el.append(this.outerHTML);
			});
		});

	$form.submit(function(e) {
		e.preventDefault();
		NBOW.ajaxForm($form, "/tag/create", {}, function(m,d){
			UBOW.clearForm($form);
			$tag_choices.append('<i  tag="' + d['id'] + '" tip="' + d['brief'] + '">' + d['name'] + '</i>');
			$('[href="#tags-select"]').tab('show');
			UBOW.flashSuccess('Tag Created: '+ d.name);
			});
		});

	function csvIDs() {
		var ids = '';
		$el.find('i.on').each(function(i,e){
			ids += (ids?',':'') + e.getAttribute('tag');
			});
		return ids;
		}

	init();
	return {
		csvIDs: csvIDs
		}
	}
