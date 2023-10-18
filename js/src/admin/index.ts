import app from 'flarum/admin/app';

app.initializers.add('mattoid-daily-check-in-history', () => {

  app.extensionData.for("mattoid-daily-check-in-history")
    .registerSetting({
      setting: 'mattoid-forum-checkin.maxSupplementaryCheckin',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.max-supplementary-checkin'),
      type: 'number',
    })
    .registerSetting({
      setting: 'mattoid-forum-checkin.spanDayCheckin',
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
      setting: 'mattoid-forum-checkin.consumption',
      label: app.translator.trans('mattoid-daily-check-in-history.admin.settings.consumption'),
      help: app.translator.trans('mattoid-daily-check-in-history.admin.settings.reward-money-requirement'),
      type: 'select',
      options: {0 : "小药店", 1 : "用户中心（日历）"},
      default: 0
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
});
