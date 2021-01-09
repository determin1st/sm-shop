"use strict"
window.addEventListener 'load', !->
	# проверка режима
	if root = document.querySelector '#customer_login'
		# форма авторизации/регистрации {{{
		# подготовка
		formBox      = root.querySelector 'div.form-box'
		formLogin    = formBox.querySelector 'form.login'
		formRegister = formBox.querySelector 'form.register'
		title        = root.querySelector 'h2'
		# определим активную форму
		activeForm = formLogin
		if formRegister and (formRegister.className.indexOf 'hidden') == -1
			activeForm = formRegister
		# установим фокус на первое поле ввода
		if a = activeForm.querySelector 'input.first'
			a.focus!
			a.select!
		# установим обработчики событий
		# переключение между формами авторизации <=> регистрации {{{
		formRegister and do !->
			# подготовка
			form = if activeForm == formRegister
				then [activeForm, formLogin]
				else [activeForm, formRegister]
			button = [
				form.0.querySelector 'button.switch'
				form.1.querySelector 'button.switch'
			]
			input = [
				form.0.querySelector 'input.first'
				form.1.querySelector 'input.first'
			]
			time = 0.6
			###
			# создаем анимацию
			anim = gsap.timeline {
				paused: true
			}
			anim.add !->
				input.0.focus! if anim.reversed!
			anim.to form.0, time/2, {
				opacity: 0
				ease: "power3.out"
			}
			anim.set form.0, {display: 'none'}
			anim.set form.1, {display: 'block'}
			anim.to form.1, time/2, {
				opacity: 1
				ease: "power3.in"
			}
			anim.add !->
				input.1.focus! if not anim.reversed!
			# дополнительная группа для анимации заголовка
			c = gsap.timeline {
				paused: true
			}
			c.to title, time/4, {
				scale: 0.5
				opacity: 0
				ease: "back.in"
			}
			c.add !->
				if anim.reversed!
					title.innerText = button.1.innerText
				else
					title.innerText = button.0.innerText
			c.to title, time/4, {
				scale: 1
				opacity: 1
				clearProps: true
				ease: "back.out"
			}
			anim.add c.play!, time/4
			###
			# установим начальную позицию
			# навешиваем обработчики
			button.0.addEventListener 'click', (e) !->
				# предотвращаем действие по-умолчанию
				e.preventDefault!
				e.stopImmediatePropagation!
				# запускаем анимацию
				if not anim.isActive!
					anim.play!
			button.1.addEventListener 'click', (e) !->
				# предотвращаем действие по-умолчанию
				e.preventDefault!
				e.stopImmediatePropagation!
				# запускаем анимацию
				if not anim.isActive!
					anim.reverse!
			# готово
		# }}}
		# }}}
	###
###
