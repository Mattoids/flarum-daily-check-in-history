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
