"use strict"
###
#if typeof SM != 'undefined'
###
do !-> # {{{
	# ленивая загрузка <iframe>
	/***
	list = [...(document.querySelectorAll 'iframe')]
	for iframe in list when 'src' of iframe.dataset
		iframe.src = iframe.dataset.src
	/***/
	# SEARCH
	(a = document.querySelector '#search') and let root = a
		# get all product nodes
		list = [...(root.querySelectorAll '.list .product')]
		list.forEach (node) !->
			# attach link handler
			node.addEventListener 'click', (e) !->>
				root.classList.add 'x'
				node.classList.add 'x'
				window.location.assign node.dataset.href
# }}}
