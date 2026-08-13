<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Entity\Setting;
use App\Entity\SubAccount;
use App\Entity\Transaction;
use App\Lib\Manager\SettingsManager;
use App\Service\EncryptionService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;
    private EncryptionService $encryption;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->encryption = new EncryptionService('test123');

        $this->createBase();

        $this->createCategories();

        $this->createDebitTransactions();

        $this->createCreditTransactions($this->faker->dateTimeBetween('first day of last month', 'last day of last month'));
        $this->createCreditTransactions($this->faker->dateTimeBetween('first day of this month', 'last day of this month'));
        $this->createCreditTransactions($this->faker->dateTimeBetween('first day of next month', 'last day of next month'));

        $manager->flush();
    }

    private function createBase(): void
    {
        $account = new Account();
        $account->setName('This is a Testaccount');
        $account->setBic($this->faker->swiftBicNumber);
        $account->setBankCode($this->faker->swiftBicNumber);
        $account->setUrl($this->faker->url);
        $account->setUsername($this->faker->userName, $this->encryption);
        $account->setPassword($this->faker->password, $this->encryption);
        $this->manager->persist($account);
        $this->manager->flush();

        $subaccount = new SubAccount();
        $subaccount->setAccount($account);
        $subaccount->setEnabled(true);
        $subaccount->setIban($this->faker->iban('DE'));
        $subaccount->setAccountNumber($this->faker->creditCardNumber);
        $subaccount->setDescription('This is a Testdescription');
        $this->setReference(SubAccount::class, $subaccount);
        $this->manager->persist($subaccount);
        $this->manager->flush();

        $setting = new Setting();
        $setting->setName(SettingsManager::SETTING_MAIN_ACCOUNT);
        $setting->setValue($subaccount->getId());
        $this->manager->persist($setting);
        $this->manager->flush();
    }

    private function createCategories(): void
    {
        $this->createMany(CategoryGroup::class, 5, function (CategoryGroup $group, int $i) {
            $group->setName(sprintf('Category Group %d', $i + 1));
        });

        $this->createMany(Category::class, 10, function (Category $category, int $i) {
            $category->setName($this->faker->word());
            $category->setCategoryGroup($this->getReference(CategoryGroup::class.'_'.($i + 1) % 3, CategoryGroup::class));
        });
    }

    private function createDebitTransactions(): void
    {
        $this->createMany(Transaction::class, 100, function (Transaction $t, int $i) {
            $t->setCategory($this->getReference(Category::class.'_'.($i + 1) % 10, Category::class));

            $dt = $this->faker->dateTimeBetween('first day of last month', 'last day of next month');
            $t->setBookingDate($dt);
            $t->setValutaDate($dt);
            $t->setAmount(intval($this->faker->randomFloat(2, 10, 500) * 100));
            $t->setCreditDebit('debit');
            $t->setName($this->faker->company);
            $t->setDescription($this->faker->text());
            $t->setDescriptionRaw($this->faker->text());
            $t->setBookingText('DIRECT DEBIT');
            $t->setAccountNumber($this->faker->iban('DE'));
            $t->setBankCode($this->faker->swiftBicNumber);
            $t->setChecksum(md5($this->faker->text));
            $t->setSubAccount($this->getReference(SubAccount::class, SubAccount::class));
        });
    }

    private function createCreditTransactions(\DateTime $dt): void
    {
        $t = new Transaction();
        $t->setCategory($this->getReference(Category::class.'_1', Category::class));
        $t->setBookingDate($dt);
        $t->setValutaDate($dt);
        $t->setAmount(intval($this->faker->randomFloat(2, 3000, 5000) * 100));
        $t->setCreditDebit('credit');
        $t->setName($this->faker->company);
        $t->setDescription($this->faker->text());
        $t->setDescriptionRaw($this->faker->text());
        $t->setBookingText('DIRECT CREDIT');
        $t->setAccountNumber($this->faker->iban('DE'));
        $t->setBankCode($this->faker->swiftBicNumber);
        $t->setChecksum(md5($this->faker->text));
        $t->setSubAccount($this->getReference(SubAccount::class, SubAccount::class));

        $this->manager->persist($t);
    }

    private function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; ++$i) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            // store for usage later as App\Entity\ClassName_#COUNT#
            $this->addReference($className.'_'.$i, $entity);
        }
    }
}
