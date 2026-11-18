<?php
declare(strict_types=1);

final class ContactController extends Controller
{
    /** Public, read-only list. Action buttons only appear for logged-in users. */
    public function index(): void
    {
        View::render('contacts/index', [
            'title'    => 'Contacts',
            'contacts' => Contact::all(),
            'isAdmin'  => Auth::check(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->form('Add contact', 'contacts/store', null, ['name' => '', 'email' => '', 'phone' => ''], []);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $data   = $this->collect();
        $errors = Contact::validate($data);

        if ($errors) {
            http_response_code(422);
            $this->form('Add contact', 'contacts/store', null, $data, $errors);
            return;
        }

        Contact::create($data);
        Flash::set('success', 'Contact added.');
        $this->redirect('contacts');
    }

    public function edit(): void
    {
        $this->requireAuth();
        $contact = $this->findOrFail($_GET);
        $this->form('Edit contact', 'contacts/update', (int) $contact['id'], $contact, []);
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $contact = $this->findOrFail($_POST);
        $data    = $this->collect();
        $errors  = Contact::validate($data);

        if ($errors) {
            http_response_code(422);
            $this->form('Edit contact', 'contacts/update', (int) $contact['id'], $data, $errors);
            return;
        }

        Contact::update((int) $contact['id'], $data);
        Flash::set('success', 'Contact updated.');
        $this->redirect('contacts');
    }

    /** Confirmation page (no JavaScript needed, works with a strict CSP). */
    public function confirmDelete(): void
    {
        $this->requireAuth();
        $contact = $this->findOrFail($_GET);
        View::render('contacts/delete', ['title' => 'Delete contact', 'contact' => $contact]);
    }

    public function delete(): void
    {
        $this->requireAuth();
        $this->requireCsrf();

        $contact = $this->findOrFail($_POST);
        Contact::delete((int) $contact['id']);
        Flash::set('success', 'Contact deleted.');
        $this->redirect('contacts');
    }

    // -------------------------------------------------------------------------

    private function collect(): array
    {
        return [
            'name'  => $this->post('name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
        ];
    }

    private function findOrFail(array $source): array
    {
        $id      = $this->intParam($source, 'id');
        $contact = $id === null ? null : Contact::find($id);
        if ($contact === null) {
            $this->notFound();
        }
        return $contact;
    }

    private function form(string $title, string $action, ?int $id, array $contact, array $errors): void
    {
        View::render('contacts/form', [
            'title'   => $title,
            'action'  => $action,
            'id'      => $id,
            'contact' => $contact,
            'errors'  => $errors,
        ]);
    }
}
