        

<img src="{{$User->image?url('public/uploads/users/'.$User->image):url('public/uploads/users/default.png')}}" alt="">
<a href="{{_url('customer/dashboard')}}" class="btn btn-profile">{{_lang('app.my_profile')}}</a>
<a href="{{_url('customer/user/edit')}}" class="btn btn-profile">{{_lang('app.edit_my_profile')}}</a>
<a href="{{_url('customer/user/notifications')}}" class="btn btn-profile">{{_lang('app.notifications')}}</a>
<a href="{{ route('logout') }}" class="btn btn-profile"
   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">{{ _lang('app.logout') }}</a>
<form id="logout-form" action="{{ _url('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>
