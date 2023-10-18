import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';

app.initializers.add('mattoid-checkin-history', () => {
  extend(UserPage.prototype, 'navItems', function (items) {
    items.add(
      'buy-doorman-store',
      <LinkButton href="/checkin/history" icon="fas fa-store">
      签到记录
      </LinkButton>,
    0
  );
  })
});
