import app from 'flarum/admin/app';
import Button from 'flarum/common/components/Button';
import SendMoneyModal from './components/SendMoneyModal';

app.initializers.add('mattoid-daily-check-in-history', () => {

  app.extensionData.for("mattoid-daily-check-in-history")
    .registerSetting(function () {
      return m('.Form-group', Button.component({
        className: 'Button',
        onclick() {
          app.modal.show(SendMoneyModal);
        },
      }, app.translator.trans('mattoid-daily-check-in-history.admin.settings.complimentary-supplementary-card')));
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.max-supplementary-checkin',
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.max-supplementary-checkin-requirement'),
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.max-supplementary-checkin'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.span-day-checkin',
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.span-day-checkin-requirement'),
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.span-day-checkin'),
      type: 'switch',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.checkin-range',
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-range-requirement'),
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-range'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.reward-money',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.reward-money'),
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.reward-money-requirement'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.consumption',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.consumption'),
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.reward-money-requirement'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.checkin-increase',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-increase'),
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-increase-requirement'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.checkin-card',
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-card-requirement'),
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-card'),
      type: 'switch',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.checkin-position',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-position'),
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-position-requirement'),
      type: 'select',
      options: {0 : "小药店", 1 : "用户中心（日历）"},
      default: 0
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.min-supplementary-date',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.min-supplementary-date'),
      default: '#2756c6',
      type: 'date',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.checkin-color',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.checkin-color'),
      default: '#2756c6',
      type: 'text',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.supplementary-color',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.supplementary-color'),
      default: '#ff9900',
      type: 'text',
    })
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.allow-supplementary-check-in'),
        permission: 'checkin.allowSupplementaryCheckIn',
      },
      'moderate',
      90
    )
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.issuance-of-supplementary-cards'),
        permission: 'checkin.issuanceOfSupplementaryCards',
      },
      'moderate',
      90
    )
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.query-others-history'),
        permission: 'checkin.queryOthersHistory',
        allowGuest: true
      }, 'view')
});
