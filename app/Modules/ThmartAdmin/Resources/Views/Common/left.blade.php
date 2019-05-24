<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      系统概况
    </div>
    <ul class="sidebar-menu">
      @foreach ($compactData['adminListCat'] as $v)
          @if ($v['fname'] == 0 && isset($v['son']))
            <li>
              <a href="#">
                <span>{{$v['name']}}</span>
                @if (isset($v['son']))
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                @endif
              </a>
              <ul class="treeview-menu">
                @if (isset($v['son']))
                  @foreach ($v['son'] as $value)
                    <li><a href="{{$value['url']}}">{{$value['name']}}</a></li>
                  @endforeach
                @endif
              </ul>
            </li>
          @endif
      @endforeach
    </ul>
  </section>
</aside>