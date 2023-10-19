import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import CheckinHistoryPage from "./pages/CheckinHistoryPage";

app.initializers.add('mattoid-checkin-history', () => {
  app.routes['user.checkin.history'] = {
    path: '/u/:username/checkin/history',
    component: CheckinHistoryPage,
  };

  extend(UserPage.prototype, 'navItems', function (items) {
    if (!app.session.user) {
      return;
    }

    items.add('post-checkin-history', LinkButton.component({
      href: app.route('user.checkin.history', {
        username: app.session.user.username(),
      }),
      icon: 'fas fa-checkin-history',
    }, app.translator.trans('mattoid-daily-check-in-history.forum.page.link-name')));
  });
});
