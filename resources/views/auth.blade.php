
<div class="modal-backdrop" x-show="isAuthModalOpen" x-cloak>
	<div class="backdrop-inner">
		<div class="modal modal-default" :class="isAuthModalOpen && 'open'">
			<button class="modal-close" @click="isAuthModalOpen = false">
				x
			</button>
			<div id="modal-content" x-data="">
 
				<div x-show="active === 'login' || !active" x-cloak>
					<h3 class="mb-20">{{__ ('home.auth') }}</h3>


					<div id="uLogin" data-ulogin="display=panel;theme=flat;fields=first_name,last_name;providers=vkontakte,facebook,google,yandex,mailru,steam;hidden=;redirect_uri=https%3A%2F%2Fmartinkazino.com%2Fauth%2Fulogin;mobilebuttons=0;"></div>
					<div class="form-group">
						<label>Email</label>
						<input placeholder="example@mail.com" x-model="email" class="form-control">
					</div>
					<div class="form-group">
						<div class="d-flex justify-content-between mg-b-5">
							<label class="mg-b-0-f">{{__ ('home.pass') }}</label>
						</div>
						<input type="password" x-model="password" placeholder="******" class="form-control mb-10">

                        <div class="text-right">
                            <a href="#" @click.prevent="authModal('reset')"> {{__ ('home.recovery') }}</a>
                        </div>
					</div> <!---->

					<div class="form-group">
						<button type="button" @click="login()" class="btn-submit"><span>{{__ ('home.login') }}</span></button>
					</div>

					<div class="text-center">
						{{__ ('home.netac') }}
						<a href="#" @click.prevent="authModal('register')"> {{__ ('home.reg') }}</a>
					</div>
				</div>

				<form method="POST" @submit.prevent="register()" x-show="active === 'register'" x-cloak>
					<h3 class="mb-20">{{__ ('home.reg') }}</h3>

					<div class="uLogin" data-ulogin="display=panel;theme=flat;fields=first_name,last_name;providers=vkontakte,facebook,google,yandex,mailru,steam;hidden=;redirect_uri=https%3A%2F%2Fmartinkazino.com%2Fauth%2Fulogin;mobilebuttons=0;"></div>
					<div class="form-group">
						<label>Login <sup>*</sup> </label>
						<input placeholder="{{__ ('home.name') }}" x-model="form.username" class="form-control">
					</div>
					<div class="form-group">
						<label>E-mail <sup>*</sup> </label>
						<input type="email" x-model="form.email" placeholder="example@gmail.com" class="form-control">
					</div>

					<div class="form-group">
						<label>{{__ ('home.pass') }} <sup>*</sup></label>
						<input type="password" x-model="form.password" placeholder="******" class="form-control">
					</div>
					<!---->
					<button class="btn-submit mb-20"><span>{{__ ('home.reg') }}</span></button>

					<div class="text-center">
						{{__ ('home.uzac') }}
						<a href="#" @click.prevent="active = 'login'">{{__ ('home.login') }}</a>
					</div>
				</form>

                <div x-show="active === 'reset'" x-cloak>
					<h3 class="mb-20">{{__ ('home.vos') }}</h3>

                    <p class="text-center mb-15">
                        <a href="#" @click.prevent="authModal('password')"> {{__ ('home.vos') }}</a>
                    </p>

					<div class="form-group">
						<label>E-mail *</label>
						<input placeholder="Email" x-model="email" class="form-control mb-10">
                        <div class="text-right">
                            <a href="#" @click.prevent="authModal('password')"> {{__ ('home.textvs') }}</a>
                        </div>
					</div>

					<div class="form-group">
						<button type="button" @click.prevent="resetPassword()" class="btn-submit"><span>{{__ ('home.gocode') }}</span></button>
					</div>

					<div class="text-center">
						<a href="#" @click.prevent="authModal('login')"> {{__ ('home.login') }}</a> {{__ ('home.or') }}
						<a href="#" @click.prevent="authModal('register')"> {{__ ('home.reg') }}</a>
					</div>
				</div>

                <div x-show="active === 'password'" x-cloak>
					<h3 class="mb-20">{{__ ('home.vos') }}</h3>

					<div class="form-group">
						<label>E-mail <sup>*</sup> </label>
						<input type="email" x-model="form.email" placeholder="example@email.com" class="form-control">
					</div>

					<div class="form-group">
						<label>{{__ ('home.newpass') }} <sup>*</sup></label>
						<input type="password" x-model="form.password" placeholder="******" class="form-control">
					</div>

					<div class="form-group">
						<label>{{__ ('home.repass') }} <sup>*</sup></label>
						<input type="password" x-model="form.password_confirmation" placeholder="******" class="form-control">
					</div>

                    <div class="form-group">
						<label>{{__ ('home.code') }}<sup>*</sup> </label>
						<input type="text" x-model="form.token" placeholder="********" class="form-control mb-10" maxlength="8">
                        <div class="text-right">
                             <a href="#" @click.prevent="authModal('reset')"> {{__ ('home.nocode') }}</a>
                        </div>
					</div>
					<div class="form-group">
						<button type="button" @click.prevent="savePassword()" class="btn-submit"><span>{{__ ('home.save') }}</span></button>
					</div>

					<div class="text-center">
						<a href="#" @click.prevent="authModal('login')"> {{__ ('home.login') }}</a> {{__ ('home.or') }}
						<a href="#" @click.prevent="authModal('register')"> {{__ ('home.reg') }}</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<style>
div#uLogin {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
}
</style>
