"use strict"
CART = document.querySelector '.sm-blocks.minicart'
CART = CART and let root = CART # {{{
	# prepare
	link  = root.querySelector 'a'
	count = link.querySelector '.count'
	smv   = SM!
	###
	smv.onLoad = !->
		count.firstChild.textContent = a = smv.config.cart.total.count
		count.classList.add 'v' if a
	count.addEventListener 'click', (e) !->
		e.preventDefault!
		e.stopImmediatePropagation!
		console.log 'test'
	link.addEventListener 'pointerenter', !->
		link.classList.add 'h'
	link.addEventListener 'pointerleave', !->
		link.classList.remove 'h'
	###
	return {
		set: (a) !->
			count.firstChild.textContent = a
			count.classList.toggle 'v', !!a
	}
# }}}
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
