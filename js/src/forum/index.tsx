import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import UserCard from 'flarum/components/UserCard';
import LinkButton from 'flarum/common/components/LinkButton';
import CheckinHistoryPage from "./pages/CheckinHistoryPage";

app.initializers.add('mattoid-checkin-history', () => {
  app.routes['user.checkin.history'] = {
    path: '/u/:username/checkin/history',
    component: CheckinHistoryPage,
  };

  extend(UserCard.prototype, 'infoItems', function (items) {
    items.add('checkinCard',
      <span>{app.translator.trans('mattoid-daily-check-in-history.forum.page.checkin-card') + '：' + this.attrs.user.data.attributes['checkinCard']}</span>
    )
  });

  extend(UserPage.prototype, 'navItems', function (items) {
    if (app.session.user.id() !== this.user.id()) {
      if (!this.user || !this.user.attribute('canQueryOthersHistory')) {
        return;
      }
    }

    items.add('post-checkin-history', LinkButton.component({
      href: app.route('user.checkin.history', {
        username: this.user.slug(),
      }),
      icon: 'fas fa-calendar-alt',
    }, app.translator.trans('mattoid-daily-check-in-history.forum.page.link-name')));
  });

});
