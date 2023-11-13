import app from 'flarum/admin/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import extractText from 'flarum/common/utils/extractText';

export default class SendMoneyModal extends Modal {
    range: boolean = false
    amount: string = ''
    email: string = ''
    username: string = ''
    isDisabled: boolean = false
    // message: string = ''
    // suspended: boolean = false
    // lastActivity: string = ''
    // previewCount: number | null = null

    className() {
        return 'Modal--small';
    }

    title() {
        return app.translator.trans('mattoid-daily-check-in-history.admin.settings.complimentary-supplementary-card');
    }

    oncreate(vnode: any) {
        super.oncreate(vnode);

        this.refresh();
    }

    content() {
        return m('.Modal-body', [
            m('.Form-group', [
              Switch.component({
                state: this.range,
                onchange: (value: boolean) => {
                  this.range = value;
                  if (this.range) {
                    this.isDisabled = true;
                  } else {
                    this.isDisabled = false;
                  }
                },
                disabled: this.loading,
              }, app.translator.trans('mattoid-daily-check-in-history.admin.settings.user-all')),
              m('.helpText', app.translator.trans('mattoid-daily-check-in-history.admin.settings.range-help')),
            ]),
            // m('.Form-group', [
            //   m('label', app.translator.trans('mattoid-daily-check-in-history.admin.settings.user-group')),
            //
            //   m('.helpText', app.translator.trans('mattoid-daily-check-in-history.admin.settings.group-help')),
            // ]),
            m('.Form-group', [
              m('label', app.translator.trans('mattoid-daily-check-in-history.admin.settings.username')),
              m('input.FormControl', {
                type: 'text',
                value: this.username,
                onchange: (event: InputEvent) => {
                  this.username = (event.target as HTMLInputElement).value;
                },
                min: 0,
                step: 0.1,
                disabled: this.loading || this.isDisabled,
              }),
              m('.helpText', app.translator.trans('mattoid-daily-check-in-history.admin.settings.user-help')),
            ]),
            m('.Form-group', [
              m('label', app.translator.trans('mattoid-daily-check-in-history.admin.settings.give-number')),
              m('input.FormControl', {
                type: 'number',
                value: this.amount,
                onchange: (event: InputEvent) => {
                  this.amount = (event.target as HTMLInputElement).value;
                },
                min: 0,
                step: 0.1,
                disabled: this.loading,
              }),
            ]),
            m('.Form-group', Button.component({
                type: 'submit',
                className: 'Button Button--primary',
                loading: this.loading,
                disabled: parseFloat(this.amount || '0') <= 0,
            }, app.translator.trans('mattoid-daily-check-in-history.admin.settings.submit-give'))),
        ]);
    }

    refresh() {

    }

    request(dryRun: boolean = false) {
        return app.request<{ userMatchCount: number }>({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/give/checkin/card',
            errorHandler: this.onerror.bind(this),
            body: {
                userid: this.userid,
                username: this.username,
                amount: this.amount,
                range: this.range,
                dryRun,
            },
        });
    }

    onsubmit(event: Event) {
        event.preventDefault();

        this.loading = true;

        this.request().then(payload => {
            this.hide();

            app.alerts.show({type: 'success'}, app.translator.trans('clarkwinkelmann-money-to-all.lib.modal.success', {
                count: payload.userMatchCount,
            }));
        }).catch(() => {
            this.loading = false;
            m.redraw();
        });
    }
}
