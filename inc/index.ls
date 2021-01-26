"use strict"
/***
do !-> # ленивая загрузка <iframe> {{{
	list = [...(document.querySelectorAll 'iframe')]
	for iframe in list when 'src' of iframe.dataset
		iframe.src = iframe.dataset.src
# }}}
do !-> # специализированное меню {{{
	# подготовка
	if not (n = document.querySelector '#site-navigation ul.menu')
		return
	###
	itemSelected = false
	activeDropdown = null
	activeTimer = null
	menuWidth = 0
	nodeMap =
		menu: n
		box: [...(n.querySelectorAll 'li')]
		item: [...(n.querySelectorAll 'li > a')]
		line: [...(n.querySelectorAll 'li > hr')]
		dropdown: [...(n.querySelectorAll '.dropdown')]
		link: [...(n.querySelectorAll '.dropdown > a')]
	# вспомогательные функции
	resizeHandler = !-> # {{{
		# определим изменилась ли ширина меню
		if (a = nodeMap.menu.clientWidth) == menuWidth
			return
		# обновляем
		menuWidth := a
		# для каждого выпадающего меню..
		for e0 in nodeMap.dropdown
			# сравним горизонтальные смещения контейнеров
			e1 = e0.parentNode
			e2 = e1.parentNode
			a  = e1.getBoundingClientRect!
			b  = e2.getBoundingClientRect!
			a  = a.left + e0.clientWidth
			b  = b.left + e2.clientWidth
			if (c = a - b) > 0
				# чтобы не было выхода за правую границу контейнера,
				# задаем отрицательное смещение
				e0.style.left = '-'+c+'px'
	# }}}
	deactivateDropdown = !-> # {{{
		# установим флаг
		itemSelected := true
		# деактивируем таймер
		if activeTimer
			window.clearTimeout activeTimer
			activeTimer := 0
		# деактивируем дропдаун (медленно)
		if activeDropdown
			activeDropdown.timeScale 0.3
			activeDropdown.reverse!
			activeDropdown := null
	# }}}
	# обработчики событий
	# корневые элементы меню {{{
	nodeMap.item.forEach (item, index) !->
		# инициализация
		# {{{
		# таймер деактивации
		dropdownTimer = 0
		# задаем начальный стиль линии
		parent = item.parentNode
		if (parent.className.indexOf 'current-menu') == -1
			gsap.set nodeMap.line[index], {
				visibility: 'visible'
				opacity: 0.4
				scale: 0
			}
		# подготовка анимации
		dropdown = gsap.timeline {
			paused: true
		}
		dropdown.to nodeMap.line[index], 0.3, {
			scale: 1
			opacity: 1
			ease: 'power2.out'
		}, 0
		if svg = item.querySelector 'svg'
			dropdown.to svg, 0.3, {
				rotate: 90
				ease: 'power2.out'
			}, 0
		# добавляем анимацию выпадающего меню
		if submenu = nodeMap.box[index].querySelector '.dropdown'
			# задаем начальный стиль
			gsap.set submenu, {
				autoAlpha: 0
				scale: 0.9
			}
			# добавляем
			dropdown.to submenu, 0.3, {
				autoAlpha: 1
				scale: 1
				ease: 'power2.inOut'
			}, 0
		# }}}
		# вспомогательные функции
		activateDropdown = !-> # {{{
			# сбрасываем таймер
			if activeTimer
				window.clearTimeout activeTimer
				activeTimer := 0
			# деактивируем текущее
			if activeDropdown and activeDropdown != dropdown
				activeDropdown.reverse!
				activeDropdown := null
			# активируем себя
			activeDropdown := dropdown if submenu
			dropdown.play!
		# }}}
		# обработчики
		# наведение курсора {{{
		item.addEventListener 'pointerenter', (e) !->
			# предотвращаем действия по-умолчанию
			e.preventDefault!
			e.stopPropagation!
			# проверка
			if itemSelected or e.pointerType != 'mouse'
				return
			# активируем выпадающее меню
			activateDropdown!
		###
		item.addEventListener 'pointerleave', (e) !->
			# предотвращаем действия по-умолчанию
			e.preventDefault!
			e.stopPropagation!
			# проверка
			if itemSelected or e.pointerType != 'mouse'
				return
			# проверяем наличие выпадающего меню
			if submenu
				# запускаем таймер деактивации
				# только для собственного меню
				if activeDropdown == dropdown
					activeTimer := window.setTimeout !->
						activeDropdown.reverse!
						activeDropdown := null
						activeTimer := 0
					, 2000
			else
				# при отсутствии выпадающего меню деактивируем наводку
				dropdown.reverse!
		# }}}
		# клик {{{
		item.addEventListener 'click', (e) !->
			# отмена всплытия
			e.stopPropagation!
			# проверим наличие выпадающего меню
			if submenu
				# предотвращаем действия по-умолчанию
				e.preventDefault!
				# проверим тип манипулятора
				if e.pointerType != 'mouse'
					activateDropdown!
			else
				# деактивируем дропдаун
				deactivateDropdown!
		# }}}
	# }}}
	# выпадающие меню {{{
	nodeMap.dropdown.forEach (item) !->
		# наведение курсора
		item.addEventListener 'pointerenter', (e) !->
			# сбрасываем таймер
			if activeTimer
				window.clearTimeout activeTimer
				activeTimer := 0
		# увод курсора
		item.addEventListener 'pointerleave', (e) !->
			# запускаем таймер деактивации
			if activeDropdown
				activeTimer := window.setTimeout !->
					activeDropdown.reverse!
					activeDropdown := null
					activeTimer := 0
				, 2000
		# клик
		item.addEventListener 'click', (e) !->
			# отменяем действие по-умолчанию и всплытие события
			e.preventDefault!
			e.stopPropagation!
	# }}}
	# элементы выпадающего меню {{{
	nodeMap.link.forEach (item) !->
		# инициализация
		hasNoLink = item.classList.contains 'nolink'
		# клик
		item.addEventListener 'click', (e) !->
			# отмена всплытия
			e.stopPropagation!
			# проверка
			if hasNoLink
				# предотвращаем действие по-умолчанию
				e.preventDefault!
			else
				# задаем класс
				item.classList.add 'hovered'
				# деактивируем дропдаун
				deactivateDropdown!
	# }}}
	# документ/окно {{{
	document.addEventListener 'click', (e) !->
		# деактивируем выпадающее меню
		if activeDropdown
			# сбрасываем таймер
			if activeTimer
				window.clearTimeout activeTimer
				activeTimer := 0
			# деактивируем
			activeDropdown.reverse!
			activeDropdown := null
	# изменение размеров
	window.addEventListener 'resize', resizeHandler
	# }}}
	# инициируем изменение размеров
	window.dispatchEvent (new Event 'resize')
# }}}
do !-> # корзина {{{
	# prepare
	if not (root = document.querySelector '.site-cart')
		return
	link = root.querySelector 'a'
	svg  = link.querySelector 'svg'
	cnt  = link.querySelector 'div > div'
	# анимация наведения
	if not root.classList.contains 'is-current'
		# создаем
		anim = gsap.timeline {
			paused: true
		}
		anim.to cnt, {
			opacity: 0
			duration: 0.2
		}, 0
		anim.to svg, {
			scale: 1.12
			duration: 0.3
			ease: 'back'
		}, 0
		# навешиваем события
		link.addEventListener 'click', !->
			anim.play! if anim
			anim := null
		link.addEventListener 'pointerenter', !->
			anim.play! if anim
		link.addEventListener 'pointerleave', !->
			anim.reverse! if anim
# }}}
do !-> # ссылки .modern-link {{{
	# get all links
	if not (link = document.querySelectorAll '.modern-link')
		return
	# initialize each
	link.forEach (node) !->
		# prepare
		if not (line = node.querySelector 'hr')
			return
		# remove noscript
		node.classList.remove 'noscript'
		# check
		if not node.classList.contains 'is-current'
			# create animation
			gsap.set line, {
				opacity: 0
				scale: 0
			}
			anim = gsap.timeline {
				paused: true
			}
			anim.to line, {
				opacity: 1
				scale: 1
				duration: 0.3
				ease: "power2.out"
			}, 0
			# set handlers
			node.addEventListener 'click', !->
				# animation should finish
				anim.play! if anim
				anim := null
			node.addEventListener 'pointerenter', !->
				anim.play! if anim
			node.addEventListener 'pointerleave', !->
				anim.reverse! if anim
# }}}
/***/
