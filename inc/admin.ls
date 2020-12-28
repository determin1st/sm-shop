"use strict"
window.addEventListener 'load', !->
	###
	# исправление бага начальной загрузки
	# gutenberg + carbon-fields при наличии post_parent_id фильтра
	while n = document.querySelector '#editor'
		# get last element
		if not n = n.querySelector '.components-panel > div:last-child'
			break
		# watch for changes
		o = new MutationObserver (list, observer) !->
			observer.disconnect!
			wp.hooks.doAction('carbon-fields.init');
		o.observe n, {childList: true}
		# done
		break
	###
###

