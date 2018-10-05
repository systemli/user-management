<?php

namespace App\Tests\Validator\Constraints;

use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use App\Validator\Constraints\Voucher as VoucherConstraint;
use App\Validator\Constraints\VoucherValidator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class VoucherValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        $voucher = new Voucher();
        $voucher->setCode('code');
        $repository = $this->getMockBuilder(VoucherRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())->method('findByCode')->willReturnMap([
            ['code', $voucher],
        ]);
        $manager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $manager->expects($this->any())->method('getRepository')->willReturn($repository);

        return new VoucherValidator($manager);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsVoucherType()
    {
        $this->validator->validate('string', new Valid());
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new VoucherConstraint(true));

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new VoucherConstraint(true));

        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new VoucherConstraint(true));
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testConstraintMissingOptions()
    {
        new VoucherConstraint();
    }

    public function testConstraintGetDefaultOption()
    {
        $constraint = new VoucherConstraint(true);
        $this->assertEquals(true, $constraint->exists);
    }

    public function testValidateVoucherInvalid()
    {
        $this->validator->validate('code2', new VoucherConstraint(true));
        $this->buildViolation('registration.voucher-invalid')
            ->assertRaised();
    }

    public function testValidateVoucherUnused()
    {
        $this->validator->validate('code', new VoucherConstraint(true));
        $this->assertNoViolation();
    }

    public function testValidateVoucherNew()
    {
        $this->validator->validate('new', new VoucherConstraint(false));
        $this->assertNoViolation();
    }

    public function testValidateVoucherNewExists()
    {
        $this->validator->validate('code', new VoucherConstraint(false));
        $this->buildViolation('registration.voucher-exists')
            ->assertRaised();
    }
}
