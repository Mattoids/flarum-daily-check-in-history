import UserPage from 'flarum/forum/components/UserPage';

export default class CheckinHistoryPage extends UserPage {

  oninit(vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  content() {
    if (
      app.session.user &&
      (app.session.user.canViewWarnings() || (this.user.id() === app.session.user.id() && this.user.visibleWarningCount() > 0))
    ) {

      return (
        <div className="CheckinHistoryUserPage">
        </div>
      );
    }
  }

  oncreate() {

  }
}
