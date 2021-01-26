// Generated by LiveScript 1.6.0
"use strict";
window.addEventListener('load', function(){
  var root, node, a;
  if (!(root = document.getElementById('auth'))) {
    console.log('auth requirements failed');
    return;
  }
  node = {
    title: root.querySelector('.titlebox'),
    form: root.querySelector('.formbox'),
    login: root.querySelector('form.login'),
    register: root.querySelector('form.register'),
    active: null
  };
  node.active = root.classList.contains('a')
    ? node.login
    : node.register;
  root.classList.add('v');
  a = node.active.querySelector('button');
  a.focus();
  if (node.register) {
    true;
  }
  /***
  if node.register
  	# prepare
  	forms = if node.active == node.login
  		then [node.active, node.register]
  		else [node.active, node.login]
  	input = [
  		forms.0
  	]
  	# create transitions
  	time = 0.6
  	anim = gsap.timeline {paused: true}
  	anim.add !->
  		node
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
  /***/
});