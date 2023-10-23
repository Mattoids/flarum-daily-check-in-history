import Modal from 'flarum/common/components/Modal';
import Alert from 'flarum/common/components/Alert';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

/**
 * This builds event details based on a FullCalendar concept of object.  CalendarPage talks to api, sends us FC payload
 */
export default class SupplementCheckinModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

  }

  title() {
    return app.translator.trans('mattoid-daily-check-in-history.forum.modal.checkin')
  }

  className() {
    return 'SupplementCheckinModal Modal--large';
  }

  content() {
    return [
      <div className="Modal-body">
        <div className="Form-group">
          <label className="label">{app.translator.trans('mattoid-daily-check-in-history.forum.modal.supplement-checkin-desc')}</label>
        </div>
        <div className="Form-group">
          <Button type="submit" className="Button Button--primary PollModal-SubmitButton" loading={this.loading}>
            {app.translator.trans('mattoid-daily-check-in-history.forum.modal.submit')}
          </Button>
        </div>
      </div>,
    ];
  }


  async onsubmit(e) {

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/supplement/checkin',
      body: {
        date: this.attrs.info.dateStr
      }
    })
    .then((result) => {
      console.log(result)
    });

    // this.hide();
  }
}
