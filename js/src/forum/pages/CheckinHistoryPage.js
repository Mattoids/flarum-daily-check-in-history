import UserPage from 'flarum/forum/components/UserPage';

export default class CheckinHistoryPage extends UserPage {

  // 初始化时候，方便放一些准备用的数据，或者用来网络请求。此时可以拿到 vnode，但是不一定拿得到真实 DOM，所以这里不推荐进行相关的 DOM 操作，比如：vnode.dom。
  oninit(vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  content() {
    if (app.session.user) {
      return (
        <div className="CheckinHistoryUserPage">
        </div>
      );
    }
  }

  // 创建成功，此时可以拿到真实 DOM 了。
  oncreate() {

  }

  // DOM 渲染刷新后。业务有刷新变动数据时候使用。
  onupdate() {

  }

  // DOM 销毁前。常用，比如我们的离开动画。
  onbeforeremove() {

  }

  // DOM 渲染刷新前。业务有刷新变动数据时候使用。
  onremove() {

  }

  // DOM 渲染刷新前。业务有刷新变动数据时候使用。
  onbeforeupdate() {

  }
}
